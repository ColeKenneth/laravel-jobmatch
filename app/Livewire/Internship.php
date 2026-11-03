<?php

namespace App\Livewire;

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

    public string $studentId = '';
    public string $companyName = '';
    public string $position = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $skillsGained = '';
    public string $supervisor = '';
    public string $supervisorEmail = '';

    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingInternshipId = null;
    public ?int $deletingInternshipId = null;

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
    public function render() : View
    {
        return view('livewire.internship');
    }
}
