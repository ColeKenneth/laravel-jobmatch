<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Internship;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class InternshipService
{
    private function validateDates(array $data) : void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->lt($startDate)) {
            throw new BusinessRuleException("End date must be on or after the start date.");
        }
    }
    public function create(array $data) : Internship
    {
        $student = Student::findOrFail($data['student_id']);

        $this->validateDates($data);

        $attributes = [
            'student_id' => $data['student_id'],
            'company_name' => trim($data['company_name']),
            'position' => trim($data['position']),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'skills_gained' => isset($data['skills_gained']) ? trim($data['skills_gained']) : null,
            'supervisor' => isset($data['supervisor']) ? trim($data['supervisor']) : null,
            'supervisor_email' => isset($data['supervisor_email']) ? strtolower(trim($data['supervisor_email'])) : null,
        ];

        $internship = Internship::create($attributes);

        Log::info("Created internship", ['id' => $internship->id, 'student_id' => $student->id]);

        return $internship;
    }

    public function delete(int $id) : void
    {
        $internship = Internship::findOrFail($id);
        $internship->delete();

        Log::info("Deleted internship", ['id' => $id]);
    }

    public function exists(int $id) : bool
    {
        return Internship::where('id', $id)->exists();
    }

    public function getAll() : Collection
    {
        return Internship::with('student')
            ->latest('start_date')
            ->get();
    }

    public function getById(int $id) : Internship
    {
        return Internship::findOrFail($id);
    }

    public function getByStudent(int $studentId) : Collection
    {
        return Internship::with('student')
            ->where('student_id', $studentId)
            ->orderByDesc('start_date')
            ->get();
    }

    public function update(int $id, array $data) : Internship
    {
        $this->validateDates($data);

        $internship = Internship::findOrFail($id);

        if (!empty($data['student_id']) && $data['student_id'] !== $internship->student_id) {
            throw new BusinessRuleException("Cannot reassign internship to a different student.");
        }

        $internship->fill([
            'company_name' => trim($data['company_name']),
            'position' => trim($data['position']),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'skills_gained' => isset($data['skills_gained']) ? trim($data['skills_gained']) : null,
            'supervisor' => isset($data['supervisor']) ? trim($data['supervisor']) : null,
            'supervisor_email' => isset($data['supervisor_email']) ? strtolower(trim($data['supervisor_email'])) : null,
        ]);

        $internship->save();

        Log::info("Updated internship", ['id' => $internship->id]);

        return $internship;
    }


}
