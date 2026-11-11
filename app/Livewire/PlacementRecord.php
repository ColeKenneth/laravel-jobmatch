<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\PlacementStatus;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\JobPosting as JobPostingModel;
use App\Models\Student;
use App\Services\PlacementService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Placement Records')]
class PlacementRecord extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $studentId = '';
    public string $jobPostingId = '';
    public string $placementDate = '';
    public string $placementStatus = '';
    public string $notes = '';

    protected function rules() : array
    {
        return [
            'studentId' => ['required', 'exists:students,id'],
            'jobPostingId' => ['required', 'exists:job_postings,id'],
            'placementDate' => ['required', 'date'],
            'placementStatus' => ['required', Rule::enum(PlacementStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function create(PlacementService $placementService) : void
    {
        $data = $this->validate();

        try {
            $placementService->create((int) $data['studentId'], $this->payload($data));
            $this->resetForm();
            $this->showSuccess('Placement record created successfully.');
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function edit(int $id, PlacementService $placementService) : void
    {
        $record = $placementService->getById($id);

        $this->editingId = $record->id;
        $this->studentId = (string) $record->student_id;
        $this->jobPostingId = (string) $record->job_posting_id;
        $this->placementDate = $record->placement_date->format('Y-m-d\TH:i');
        $this->placementStatus = $record->placement_status->value;
        $this->notes = $record->notes ?? '';

        $this->resetValidation();
    }

    public function update(PlacementService $placementService) : void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $placementService->update($this->editingId, $this->payload($data) + [
                    'student_id' => (int) $data['studentId'],
                ]);
            $this->resetForm();
            $this->showSuccess('Placement record was updated successfully.');
        } catch (BusinessRuleException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function delete(PlacementService $placementService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $placementService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    protected function resetForm() : void
    {
        $this->reset([
            'studentId',
            'jobPostingId',
            'placementDate',
            'placementStatus',
            'notes',
            'editingId',
        ]);

        $this->resetValidation();
    }

    protected function payload(array $data) : array
    {
        return [
            'job_posting_id' => (int) $data['jobPostingId'],
            'placement_date' => $data['placementDate'],
            'placement_status' => $data['placementStatus'],
            'notes' => $data['notes'] === '' ? null : $data['notes'],
        ];
    }

    public function render(PlacementService $placementService) : View
    {
        return view('livewire.placement-record', [
            'placements' => $placementService->getAll($this->search ?: null),
            'students' => Student::query()->get(),
            'jobPostings' => JobPostingModel::query()->orderBy('title')->get(['id', 'title']),
            'statuses' => PlacementStatus::cases(),
        ]);
    }
}
