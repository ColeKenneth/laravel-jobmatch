<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AlreadyExistsException;
use App\Models\JobPosting;
use App\Models\JobSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class JobSkillService
{
    public function create(int $jobPostingId, array $data) : JobSkill
    {
        JobPosting::findOrFail($jobPostingId);
        Skill::findOrFail($data['skill_id']);

        $alreadyAttached = JobSkill::where('job_posting_id', $jobPostingId)
            ->where('skill_id', $data['skill_id'])
            ->exists();

        if ($alreadyAttached) {
            throw new AlreadyExistsException("Skill is already attached to this job posting.");
        }

        $attributes = [
            'job_posting_id' => $jobPostingId,
            'skill_id' => $data['skill_id'],
            'is_required' => true,
            'min_proficiency_level' => $data['min_proficiency_level'] ?? null,
            'priority' => $data['priority'] ?? null,
        ];

        $jobSkill = JobSkill::create($attributes);

        Log::info("Attached skill to job", [
            'skill_id' => $jobSkill->skill_id,
            'job_posting_id' => $jobPostingId
        ]);

        return $jobSkill;
    }

    public function exists(int $jobPostingId, int $skillId) : bool
    {
        return JobSkill::where('job_posting_id', $jobPostingId)
            ->where('skill_id', $skillId)
            ->exists();
    }

    public function getByJobPosting(int $jobPostingId) : Collection
    {
        return JobSkill::where('job_posting_id')
            ->orderByDesc('priority')
            ->get();
    }

    public function removeAllForJobPosting(int $jobPostingId) : void
    {
        $deletedCount = JobSkill::where('job_posting_id', $jobPostingId)->delete();

        if ($deletedCount === 0) {
            JobPosting::findOrFail($jobPostingId);
        }

        Log::info("Removed all skills from job posting", ['job_posting_id' => $jobPostingId]);
    }

    public function removeSkill(int $id) : void
    {
        $deleted = JobSkill::where('id', $id)->delete();

        if ($deleted === 0) {
            throw new ModelNotFoundException()->setModel(JobSkill::class);
        }

        Log::info("Removed job skill", ['id' => $id]);
    }

    public function updateSkill(int $id, array $data) : JobSkill
    {
        $jobSkill = JobSkill::findOrFail($id);

        $jobSkill->fill([
            'is_required' => $data['is_required'] ?? false,
            'min_proficiency_level' => $data['min_proficiency_level'] ?? null,
            'priority' => $data['priority'] ?? null,
        ]);

        Log::info("Updated job skill", ['id' => $id]);

        return $jobSkill;
    }
}
