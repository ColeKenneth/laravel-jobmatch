<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\EmploymentHistoryService;
use App\Traits\HandlesCrud;
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
    use HandlesCrud;

    public string $alumniId = '';
    public string $companyName = '';
    public string $position = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $isCurrent = '';
    public string $responsibilities = '';

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
            $employmentHistoryService->create($this->payload($data));
            $this->resetForm();
            $this->showSuccess("Employment history was created successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('alumniId', $e->getMessage());
        }
    }

    public function edit(int $id, EmploymentHistoryService $employmentHistoryService) : void
    {
        $employmentHistory = $employmentHistoryService->getById($id);

        $this->editingId = $employmentHistory->id;
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
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $employmentHistoryService->update($this->editingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess('Employment history was updated successfully.');
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('alumniId', $e->getMessage());
        }
    }

    public function delete(EmploymentHistoryService $employmentHistoryService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $employmentHistoryService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'alumniId',
            'companyName',
            'position',
            'startDate',
            'endDate',
            'isCurrent',
            'responsibilities',
            'editingId'
        ]);
    }

    protected function payload(array $data) : array
    {
        return [
            'alumni_id' => $data['alumniId'],
            'company_name' => $data['companyName'],
            'position' => $data['position'],
            'start_date' => $data['startDate'],
            'endDate' => $data['endDate'] ?: null,
            'is_current' => $data['isCurrent'],
            'responsibilities' => $data['responsibilities'] ?: null,
        ];
    }


    public function render(EmploymentHistoryService $employmentHistoryService) : View
    {
        return view('livewire.employment-history', [
            'employmentHistories' => $employmentHistoryService->getAll(),
        ]);
    }
}
