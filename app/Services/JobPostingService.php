<?php
declare(strict_types=1);

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
   private function validateRules(array $data, ?JobPosting $jobPosting) : void
   {
       $minSalary = array_key_exists('min_salary', $data) ? $data['min_salary'] : $jobPosting?->min_salary;
       $maxSalary = array_key_exists('max_salary', $data) ? $data['max_salary'] : $jobPosting->max_salary;

       if ($minSalary !== null && $maxSalary !== null && (float) $minSalary > (float) $maxSalary) {
           throw new BusinessRuleException("Minimum salary cannot be greater than the maximum salary.");
       }

       if (array_key_exists('deadline', $data) && $data['deadline'] !== null && Carbon::parse($data['deadline'])->isPast()) {
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
       $jobPosting = JobPosting::findOrFail($id);

       $jobPosting->is_active = $isActive;
       $jobPosting->save();

       Log::info("{$action} job posting", ['id' => $jobPosting->id]);
   }

   public function incrementViews(int $id) : void
   {
       $jobPosting = JobPosting::findOrFail($id);
       $jobPosting->increment('views');
   }

   public function incrementApplications(int $id) : void
   {
       $jobPosting = JobPosting::findOrFail($id);
       $jobPosting->increment('applications');
   }

   public function update(int $id, array $data) : JobPosting
   {
       $jobPosting = JobPosting::findOrFail($id);
       $this->validateRules($data, $jobPosting);

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
           |> (fn($x) => JobPosting::whereRaw('LOWER(location) = ?', [$x]));

       return $query->latest('posted_date')->get();
   }

   public function getById(int $id) : JobPosting
   {
       return JobPosting::findOrFail($id);
   }


}
