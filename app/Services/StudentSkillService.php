<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BusinessRuleException;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class StudentSkillService
{
    public function addSkill(int $studentId, array $data) : StudentSkill
    {
        Student::findOrFail($studentId);
        Skill::findOrFail($data['skill_id']);

        $alreadyAttached = StudentSkill::where('student_id', $studentId)
            ->where('skill_id', $data['skill_id'])
            ->exists();

        if ($alreadyAttached) {
            throw new AlreadyExistsException("A skill is already attached to this student.");
        }

        $attributes = [
            'student_id' => $studentId,
            'skill_id' => $data['skill_id'],
            'proficiency_level' => 1,
            'years_of_experience' => $data['years_of_experience'] ?? null
        ];

        $studentSkill = StudentSkill::create($attributes);

        Log::info("Created student skill", [
            'student_id' => $studentId,
            'skill_id'=> $studentSkill->skill_id
        ]);

        return $studentSkill;
    }

    public function getById(int $id) : StudentSkill
    {
        return StudentSkill::findOrFail($id);
    }

    public function getByStudent(int $studentId) : Collection
    {
         return StudentSkill::where('student_id', $studentId)
             ->orderByDesc('years_of_experience')
             ->get();
    }

    public function removeAllForStudent(int $studentId) : void
    {
        $deletedCount = StudentSkill::where('student_id', $studentId)->delete();

        if ($deletedCount === 0) {
            StudentSkill::findOrFail($studentId);
        }

        Log::info("Removed all skills for student", ['student_id' => $studentId]);
    }

    public function removeSkill(int $id) : void
    {
        $studentSkill = StudentSkill::findOrFail($id);
        $studentSkill->delete();

        Log::info("Deleted student skill", ['id' => $id]);
    }

    public function updateSkill(int $id, array $data) : StudentSkill
    {
        $studentSkill = StudentSkill::findOrFail($id);

        if (!empty($data['student_id']) && $data['student_id'] !== $studentSkill->student_id) {
            throw new BusinessRuleException("Cannot reassign student skill to a different student.");
        }

        $studentSkill->fill([
            'proficiency_level' => $data['proficiency_level'],
            'years_of_experience' => $data['years_of_experience'] ?? null,
        ]);

        $studentSkill->save();

        Log::info("Updated student skill", ['id' => $id]);

        return $studentSkill;
    }


}
