<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Enum\UserRole;
use App\Enum\YearLevel;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\User;
use App\Models\Student as StudentModel;
use App\Services\StudentService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Student extends Component
{
    use WithPagination;

    public string $userId = '';
    public string $studentIdNumber = '';
    public string $program = '';
    public string $yearLevel = '';
    public string $gwa = '';
    public string $graduationDate = '';
    public string $resumeFilePath = '';
    public string $resumeText = '';
    public string $successMessage = '';
    public bool $showSuccessModal = false;
    public ?int $editingStudentId = null;
    public ?int $deletingStudentId = null;
    public bool $showDeleteModal = false;
    public string $search = '';

    protected function rules() : array
    {
        return [
            'userId' => 'required', 'exists:users,id', Rule::unique('students', 'user_id')->ignore($this->editingStudentId),
            'studentIdNumber' => 'required|string|max:30|unique:students,student_id_number', Rule::unique('students', 'student_id_number')->ignore($this->editingStudentId),
            'program' => 'required|string|max:100',
            'yearLevel' => 'required', Rule::enum(YearLevel::class),
            'gwa' => 'required|numeric|between:1.00,5.00',
            'graduationDate' => 'nullable|date',
            'resumeFilePath' => 'nullable|string|max:100',
            'resumeText' => 'nullable|string',
        ];
    }

    public function create(StudentService $studentService) : void
    {
        $data = $this->validate();

        try {
            $studentService->create($data['studentIdNumber'], [
                'user_id' => $data['userId'],
                'program' => $data['program'],
                'year_level' => $data['yearLevel'],
                'gwa' => $data['gwa'],
                'graduation_date' => $data['graduationDate'] ?: null,
                'resume_file_path' => $data['resumeFilePath'] ?: null,
                'resume_text' => $data['resumeText'] ?: null,
            ]);

            $this->resetForm();

            $this->successMessage = "Student profile was created successfully.";
            $this->showSuccessModal = true;
        }  catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function edit(int $id, StudentService $studentService) : void
    {
        $student = $studentService->getById($id);

        $this->editingStudentId = $student->id;

        $this->userId = (string) $student->user_id;
        $this->studentIdNumber = $student->student_id_number;
        $this->program = $student->program;
        $this->yearLevel = $student->year_level->value ?? $student->year_level;
        $this->gwa = (string) $student->gwa;
        $this->graduationDate = $student->graduation_date?->format('Y-m-d') ?? '';
        $this->resumeFilePath = $student->resume_file_path ?? '';
        $this->resumeText = $student->resume_text ?? '';

        $this->resetValidation();
    }

    public function update(StudentService $studentService) : void
    {
        if ($this->editingStudentId === null) {
            return;
        }

        $data = $this->validate();

        try {
            $studentService->update($this->editingStudentId, [
                'user_id' => $data['userId'],
                'student_id_number' => $data['studentIdNumber'],
                'program' => $data['program'],
                'year_level' => $data['yearLevel'],
                'gwa' => $data['gwa'],
                'graduation_date' => $data['graduationDate'] ?: null,
                'resume_file_path' => $data['resumeFilePath'] ?: null,
                'resume_text' => $data['resumeText'] ?: null,
            ]);

            $this->resetForm();

            $this->successMessage = "Student profile was updated successfully.";
            $this->showSuccessModal = true;
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentIdNumber', $e->getMessage());
        }
    }

    public function confirmDelete(int $id) : void
    {
        $this->deletingStudentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(StudentService $studentService) : void
    {
        if ($this->deletingStudentId === null) {
            return;
        }

        $studentService->delete($this->deletingStudentId);

        $this->deletingStudentId = null;
        $this->showDeleteModal = false;
    }

    public function cancelEdit() : void
    {
        $this->resetForm();
    }

    public function closeDeleteModal() : void
    {
        $this->deletingStudentId = null;
        $this->showDeleteModal = false;
    }

    public function updatedSearch() : void
    {
        $this->resetPage();
    }
    private function resetForm() : void
    {
        $this->reset([
            'userId',
            'studentIdNumber',
            'program',
            'yearLevel',
            'gwa',
            'graduationDate',
            'resumeFilePath',
            'resumeText',
            'editingStudentId'
        ]);
    }

    public function render(StudentService $studentService) : View
    {
        $eligibleUsers = User::query()
            ->where('role', UserRole::STUDENT->value)
            ->whereNotIn('id', StudentModel::query()->select('user_id'))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);


        return view('livewire.student', [
            'students' => $studentService->getAll($this->search ?: null),
            'eligibleUsers' => $eligibleUsers,
        ]);
    }
}
