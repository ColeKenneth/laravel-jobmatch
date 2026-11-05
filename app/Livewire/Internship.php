<?php

namespace App\Livewire;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Services\InternshipService;
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Internship')]
class Internship extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $studentId = '';
    public string $companyName = '';
    public string $position = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $skillsGained = '';
    public string $supervisor = '';
    public string $supervisorEmail = '';
    protected function rules() : array
    {
        return [
            'studentId' => ['required', 'exists:students,id',
                Rule::unique('internships', 'student_id')->ignore($this->editingInternshipId)],
            'companyName' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:50'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'skillsGained' => ['nullable', 'string'],
            'supervisor' => ['nullable', 'string'],
            'supervisorEmail' => ['nullable', 'email'],
        ];
    }

    public function create(InternshipService $internshipService) : void
    {
        $data = $this->validate();

        try {

            $internshipService->create($this->payload($data));

            $this->resetForm();
            $this->showSuccess('Internship created.');
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
    }

    public function edit(int $id, InternshipService $internshipService) : void
    {
        $internship = $internshipService->getById($id);

        $this->editingId = $id;
        $this->studentId = (string) $internship->student_id;
        $this->companyName = $internship->company_name;
        $this->position = $internship->position;
        $this->startDate = $internship->start_date->format('Y-m-d');
        $this->endDate = $internship->end_date?->format( 'Y-m-d') ?? '';
        $this->skillsGained = $internship->skills_gained ?? '';
        $this->supervisor = $internship->supervisor ?? '';
        $this->supervisorEmail = $internship->supervisor_email ?? '';

        $this->resetValidation();
    }

    public function update(InternshipService $internshipService) : void
    {
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $internshipService->update($this->editingId, $this->payload($data));

            $this->resetForm();
            $this->showSuccess('Internship updated.');
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
    }

    public function delete(InternshipService $internshipService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $internshipService->delete($this->deletingId);
        $this->closeDeleteModal();
    }

    private function resetForm() : void
    {
        $this->reset([
            'studentId',
            'companyName',
            'position',
            'startDate',
            'endDate',
            'skillsGained',
            'supervisor',
            'supervisorEmail',
            'editingId'
        ]);

        $this->resetValidation();
    }

    private function payload(array $data) : array
    {
        return [
            'student_id' => $data['studentId'],
            'company_name' => $data['companyName'],
            'position' => $data['position'],
            'start_date' => $data['startDate'],
            'end_date' => $data['endDate'],
            'skills_gained' => $data['skillsGained'] ?: null,
            'supervisor' => $data['supervisor'] ?: null,
            'supervisor_email' => $data['supervisorEmail'] ?: null,
        ];
    }
    public function render() : View
    {
        return view('livewire.internship');
    }
}
