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

    public string $search = '';

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
            $internshipService->create([
                'student_id' => $data['studentId'],
                'company_name' => $data['companyName'],
                'position' => $data['position'],
                'start_date' => $data['startDate'],
                'end_date' => $data['endDate'],
                'skills_gained' => $data['skillsGained'] ?: null,
                'supervisor' => $data['supervisor'] ?: null,
                'supervisor_email' => $data['supervisorEmail'] ?: null,
            ]);

            $this->resetForm();
            $this->successMessage = "Internship created successfully.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentId', $e->getMessage());
        }
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
            'editingInternshipId'
        ]);

        $this->resetValidation();
    }
    public function render() : View
    {
        return view('livewire.internship');
    }
}
