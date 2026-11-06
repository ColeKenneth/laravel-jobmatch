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
use App\Traits\HandlesCrud;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Student extends Component
{
    use WithPagination;
    use HandlesCrud;

    public string $userId = '';
    public string $studentIdNumber = '';
    public string $program = '';
    public string $yearLevel = '';
    public string $gwa = '';
    public string $graduationDate = '';
    public string $resumeFilePath = '';
    public string $resumeText = '';

    protected function rules() : array
    {
        return [
            'userId' => ['required', 'exists:users,id',
                Rule::unique('students', 'id')->ignore($this->editingStudentId)],
            'studentIdNumber' => ['required', 'string', 'max:30', 'unique:students,student_id_number',
                Rule::unique('students', 'student_id_number')->ignore($this->editingStudentId)],
            'program' => ['required', 'string', 'max:100'],
            'yearLevel' => ['required', Rule::enum(YearLevel::class)],
            'gwa' => ['required', 'numeric', 'between:1.00,5.00'],
            'graduationDate' => ['nullable', 'date'],
            'resumeFilePath' => ['nullable', 'string', 'max:100'],
            'resumeText' => ['nullable', 'string'],
        ];
    }

    public function create(StudentService $studentService) : void
    {
        $data = $this->validate();

        try {
            $studentService->create($this->studentIdNumber, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Student created successfully.");
        }  catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('userId', $e->getMessage());
        }
    }

    public function edit(int $id, StudentService $studentService) : void
    {
        $student = $studentService->getById($id);

        $this->editingId = $student->id;
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
        if (!$this->isEditing()) {
            return;
        }

        $data = $this->validate();

        try {
            $studentService->update($this->editingId, $this->payload($data));
            $this->resetForm();
            $this->showSuccess("Student updated successfully.");
        } catch (AlreadyExistsException | BusinessRuleException $e) {
            $this->addError('studentIdNumber', $e->getMessage());
        }
    }

    public function delete(StudentService $studentService) : void
    {
        if ($this->deletingId === null) {
            return;
        }

        $studentService->delete($this->deletingId);
        $this->closeDeleteModal();
    }
    protected function resetForm() : void
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
            'editingId'
        ]);
    }

    protected function payload(array $data) : array
    {
        return [
            'user_id' => $data['userId'],
            'student_id_number' => $data['studentIdNumber'],
            'program' => $data['program'],
            'year_level' => $data['yearLevel'],
            'gwa' => $data['gwa'],
            'graduation_date' => $data['graduationDate'] ?: null,
            'resume_file_path' => $data['resumeFilePath'] ?: null,
            'resume_text' => $data['resumeText'] ?: null,
        ];
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
