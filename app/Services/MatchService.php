<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\JobPosting;
use App\Models\Student;
use Illuminate\Support\Collection;

final class MatchService
{
   public function score(Student $student, JobPosting $job) : int
   {
       $studentSkills = $student->studentSkills()->pluck('skill_id');
       $requiredSkills = $job->requiredSkills()->where('is_required', true)->pluck('skill_id');

       if ($requiredSkills->isEmpty()) {
           return 0;
       }

       $matched = $studentSkills->intersect($requiredSkills)->count();

       return (int) round(($matched / $requiredSkills->count()) * 100);
   }

   public function rankJobsForStudent(Student $student) : Collection
   {
       return JobPosting::where('is_active', true)
           ->get()
           ->map(fn (JobPosting $job) => [
               'job' => $job,
               'score' => $this->score($student, $job),
           ])
           ->sortByDesc('score')
           ->values();
   }
}
