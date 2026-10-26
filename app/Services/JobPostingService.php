<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Employer;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class JobPostingService
{
   private function validateRules(array $data) : void
   {
       if (isset($data['min_salary']) && isset($data['max_salary']) && $data['min_salary'] > $data['max_salary']) {
           throw new BusinessRuleException("Minimum salary cannot be greater than the maximum salary.");
       }

       if (isset($data['deadline']) && Carbon::parse($data['deadline'])->isPast()) {
           throw new BusinessRuleException("Deadline must be in the future.");
       }
   }

   public function create(array $data) : JobPosting
   {
       $employer = Employer::findOrFail($data['employer_id']);

       $this->validateRules($data);

       $attributes = [
           'employer_id' => $data['employer_id'],
           'title' => trim($data['title']),
           'description' => trim($data['description']),
           'requirements' => trim($data['requirements']),
           'preferred_skills' => isset($data['preferred_skills']) ? trim($data['preferred_skills']) : null,
           'min_experience_years' => $data['min_experience_years'],
           'min_salary' => $data['min_salary'] ?? null,
           'max_salary' => $data['max_salary'] ?? null,
           'location' => trim($data['location']),
           'employment_type' => trim($data['employment_type']),
           'posted_date' => Carbon::now(),
           'deadline' => $data['deadline'] ?? null,
           'is_active' => true,
           'views' => 0,
           'applications' => 0
       ];

       $jobPosting = JobPosting::create($attributes);

       Log::info("Created job posting", ['id' => $jobPosting->id, 'employer_id' => $employer->id]);

       return $jobPosting;
   }

   public function close(int $id) : void
   {
       $this->toggleStatus($id, false, "Closed");
   }

   public function open(int $id) : void
   {
       $this->toggleStatus($id, true, "Reopened");
   }

   private function toggleStatus(int $id, bool $isActive, string $action) : void
   {
       $updatedCount = (int) JobPosting::where('id', $id)->update(['is_active' => $isActive]);

       if ($updatedCount === 0) {
           throw new ModelNotFoundException()->setModel(JobPosting::class);
       }

       Log::info("{$action} job posting", ['id' => $id]);
   }

   public function incrementViews(int $id) : void
   {
       $updatedCount = (int) JobPosting::where('id', $id)->increment('views');

       if ($updatedCount === 0) {
           throw new ModelNotFoundException()->setModel(JobPosting::class);
       }
   }

   public function incrementApplications(int $id) : void
   {
       $updatedCount = (int) JobPosting::where('id', $id)->increment('applications');

       if ($updatedCount === 0) {
           throw new ModelNotFoundException()->setModel(JobPosting::class);
       }
   }

   public function update(int $id, array $data) : JobPosting
   {
       $this->validateRules($data);

       $jobPosting = JobPosting::findOrFail($id);

       $jobPosting->fill([
           'title' => trim($data['title']),
           'description' => trim($data['description']),
           'requirements' => trim($data['requirements']),
           'preferred_skills' => isset($data['preferred_skills']) ? trim($data['preferred_skills']) : null,
           'min_experience_years' => $data['min_experience_years'],
           'min_salary' => $data['min_salary'] ?? null,
           'max_salary' => $data['max_salary'] ?? null,
           'location' => trim($data['location']),
           'employment_type' => trim($data['employment_type']),
           'deadline' => $data['deadline'] ?? null,
           'is_active' => $data['is_active']
       ]);

       $jobPosting->save();

       Log::info("Updated job posting", ['id' => $id]);

       return $jobPosting;
   }

   public function getAll() : Collection
   {
       return JobPosting::latest('posted_date')->get();
   }

   public function getActiveJobs() : Collection
   {
       return JobPosting::where('is_active', true)
           ->where(function ($query) {
               $query->whereNull('deadline')
                   ->orWhere('deadline', '>', Carbon::now());
           })
           ->latest('posted_date')
           ->get();
   }

   public function getByEmployer(int $employerId) : Collection
   {
       return JobPosting::where('employer_id', $employerId)
           ->latest('posted_date')
           ->get();
   }

   public function getByLocation(string $location) : Collection
   {
       $query = $location
           |> trim(...)
           |> strtolower(...)
           |> (fn($x) => JobPosting::whereRaw('LOWER(location) = :location', [$x]));

       return $query->latest('posted_date')->get();
   }

   public function getById(int $id) : JobPosting
   {
       return JobPosting::findOrFail($id);
   }


}
