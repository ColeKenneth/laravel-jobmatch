<?php

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\EmploymentHistoryService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Employment History')]
class EmploymentHistory extends Component
{
    use WithPagination;

    public string $alumniId = '';
    public string $companyName = '';
    public string $position = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $isCurrent = '';
    public string $responsibilities = '';

    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingEmploymentHistoryId = null;
    public ?int $deletingEmploymentHistoryId = null;

    public string $search = '';

    protected function rules() : array
    {
        return [
            'alumniId' => ['required', 'exists:alumnis,id'],
            'companyName' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after:startDate'],
            'isCurrent' => ['required', 'bool'],
            'responsibilities' => ['nullable', 'string'],
        ];
    }

    public function create(EmploymentHistoryService $employmentHistoryService) : void
    {
        $data = $this->validate();

        try {
            $employmentHistoryService->create([
                'alumni_id' => $data['alumniId'],
                'company_name' => $data['companyName'],
                'position' => $data['position'],
                'start_date' => $data['startDate'],
                'endDate' => $data['endDate'] ?: null,
                'is_current' => $data['isCurrent'],
                'responsibilities' => $data['responsibilities'] ?: null,
            ]);

            $this->resetForm();
            $this->successMessage = "Employment history was added successfully.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('alumniId', $e->getMessage());
        }
    }

    public function closeSuccessModal() : void
    {
        $this->showSuccessModal = false;
    }

    public function edit(int $id, EmploymentHistoryService $employmentHistoryService) : void
    {
        $employmentHistory = $employmentHistoryService->getById($id);

        $this->editingEmploymentHistoryId = $employmentHistory->id;
        $this->alumniId = (string) $employmentHistory->alumni_id;
        $this->position = $employmentHistory->position;
        $this->startDate = $employmentHistory->start_date->format('Y-m-d');
        $this->endDate = $employmentHistory->end_date?->format('Y-m-d') ?? '';
        $this->isCurrent = (string) $employmentHistory->is_current;
        $this->responsibilities = $employmentHistory->responsibilities ?? '';

        $this->resetValidation();
    }

    public function update(EmploymentHistoryService $employmentHistoryService) : void
    {
        if ($this->editingEmploymentHistoryId === null) {
            return;
        }

        $data = $this->validate();

        try {
            $employmentHistoryService->update($this->editingEmploymentHistoryId, [
                'alumni_id' => $data['alumniId'],
                'position' => $data['position'],
                'start_date' => $data['startDate'],
                'endDate' => $data['endDate'] ?: null,
                'is_current' => $data['isCurrent'],
                'responsibilities' => $data['responsibilities'] ?: null,
            ]);

            $this->resetForm();
            $this->successMessage = 'Employment history was updated successfully.';
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('alumniId', $e->getMessage());
        }
    }
    
    private function resetForm() : void
    {
        $this->reset([
            'alumniId',
            'companyName',
            'position',
            'startDate',
            'endDate',
            'isCurrent',
            'responsibilities',
            'editingEmploymentHistoryId'
        ]);
    }


    public function render() : View
    {
        return view('livewire.employment-history');
    }
}
