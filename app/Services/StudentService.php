<?php
declare(strict_types=1);

namespace App\Services;

use App\Enum\UserRole;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class StudentService
{
    public function create(string $studentIdNumber, array $data) : Student
    {
        $user = User::findOrFail($data['user_id']);
        $normalizedStudentIdNumber = strtoupper(trim($studentIdNumber));

        if ($user->role != UserRole::STUDENT) {
            throw new BusinessRuleException("User must have the student role.");
        }

        $userAlreadyHasStudentProfile = Student::where('user_id', $user->id)->exists();

        if ($userAlreadyHasStudentProfile) {
            throw new AlreadyExistsException("Student already exists for user $user->id.");
        }

        $exists = Student::where('student_id_number', $normalizedStudentIdNumber)->exists();

        if ($exists) {
            throw new AlreadyExistsException("Student $normalizedStudentIdNumber exists.");
        }

        $attributes = [
            'user_id' => $data['user_id'],
            'student_id_number' => $normalizedStudentIdNumber,
            'program' => trim($data['program']),
            'year_level' => trim($data['year_level']),
            'gwa' => $data['gwa'],
            'graduation_date' => Carbon::parse($data['graduation_date']),
            'resume_file_path' => isset($data['resume_file_path']) ? trim($data['resume_file_path']) : null,
            'resume_text' => isset($data['resume_text']) ? trim($data['resume_text']) : null,
        ];

        $student = Student::create($attributes);

        Log::info("Created student profile", [
            'student_id' => $student->id,
            'user_id' => $user->id
        ]);

        return $student;
    }

    public function delete(int $id) : void
    {
        $student = Student::findOrFail($id);
        $student->delete();

        Log::info("Deleted student", ['id' => $id]);
    }

    public function exists(int $id) : bool
    {
        return Student::where('id', $id)->exists();
    }

    public function getAll() : Collection
    {
        return Student::with('user')
            ->orderByDesc('gwa')
            ->get();
    }

    public function getById(int $id) : Student
    {
        return Student::findOrFail($id);
    }

    public function getByStudentIdNumber(string $studentIdNumber) : Student
    {
        $normalizedStudentIdNumber = strtoupper(trim($studentIdNumber));

        return Student::findOrFail($normalizedStudentIdNumber);
    }

    public function getByUserId(int $userId) : Student
    {
        return Student::where('user_id', $userId)->firstOrFail();
    }

    public function studentIdNumberExists(string $studentIdNumber) : bool
    {
        $normalizedStudentIdNumber = strtoupper(trim($studentIdNumber));

        return Student::where('student_id_number', $normalizedStudentIdNumber)->exists();
    }

    public function update(int $id, array $data) : Student
    {
        $student = Student::findOrFail($id);

        if (!empty($data['user_id']) && (int)$data['user_id'] !== (int)$student->user_id) {
            throw new BusinessRuleException("Cannot reassign a student profile to a different user.");
        }

        $newStudentIdNumber = isset($data['student_id_number'])
            ? strtoupper(trim($data['student_id_number']))
            : $student->student_id_number;

        $exists = Student::where('student_id_number', $newStudentIdNumber)
            ->where('id', '!=', $student->id)
            ->exists();

        if ($exists) {
            throw new AlreadyExistsException("Student ID $newStudentIdNumber is already taken.");
        }

        $student->fill([
            'student_id_number' => $newStudentIdNumber,
            'program' => trim($data['program']),
            'year_level' => trim($data['year_level']),
            'gwa' => $data['gwa'],
            'graduation_date' => Carbon::parse($data['graduation_date']),
            'resume_file_path' => isset($data['resume_file_path']) ? trim($data['resume_file_path']) : null,
            'resume_text' => isset($data['resume_text']) ? trim($data['resume_text']) : null,
        ]);

        $student->save();

        Log::info("Updated student profile", ['id' => $id]);

        return $student;
    }

    public function updateResume(int $id, string $filePath, string $resumeText) : void
    {
        $updatedCount = (int)Student::where('id', $id)->update([
            'resume_file_path' => $filePath,
            'resume_text' => $resumeText
        ]);

        if ($updatedCount === 0) {
            throw new ModelNotFoundException()->setModel(Student::class, [$id]);
        }

        Log::info("Updated resume for student", ['id' => $id]);
    }
}
