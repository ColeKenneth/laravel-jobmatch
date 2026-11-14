<?php

namespace App\Http\Controllers;

use App\Enum\CompanySize;
use App\Enum\EmployerIndustry;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobMatchPublicController extends Controller
{
    public function home(): View
    {
        return view('jobmatch.home', ['jobs' => $this->openJobs()->latest('posted_date')->limit(3)->get()]);
    }

    public function jobs(Request $request): View
    {
        $input = $request->validate([
            'q' => 'nullable|string|max:150',
            'type' => 'nullable|in:full_time,part_time,internship,contract,freelance',
            'level' => 'nullable|in:entry,mid,senior,experienced',
            'posted' => 'nullable|in:1,7,30',
        ]);
        $jobs = $this->openJobs();
        if ($q = $input['q'] ?? null) {
            $jobs->where(fn ($query) => $query->where('title', 'like', "%{$q}%")->orWhere('location', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%")->orWhereHas('employer', fn ($company) => $company->where('company_name', 'like', "%{$q}%")));
        }
        if ($type = $input['type'] ?? null) {
            $jobs->where('employment_type', $type);
        }
        if (($input['level'] ?? '') === 'entry') {
            $jobs->where('min_experience_years', '<=', 0);
        }
        if (($input['level'] ?? '') === 'mid') {
            $jobs->whereBetween('min_experience_years', [1, 3]);
        }
        if (($input['level'] ?? '') === 'senior') {
            $jobs->where('min_experience_years', '>=', 4);
        }
        if (($input['level'] ?? '') === 'experienced') {
            $jobs->where('min_experience_years', '>', 0);
        }
        if ($days = $input['posted'] ?? null) {
            $jobs->where('posted_date', '>=', now()->subDays((int) $days));
        }

        return view('jobmatch.jobs', ['jobs' => $jobs->latest('posted_date')->orderByDesc('id')->paginate(3)->withQueryString()]);
    }

    public function job(JobPosting $job): View
    {
        abort_unless($this->openJobs()->whereKey($job->id)->exists(), 404);

        return view('jobmatch.job', ['job' => $job->load('employer.user', 'requiredSkills.skill')]);
    }

    public function skills(Request $request): View
    {
        $input = $request->validate([
            'q' => 'nullable|string|max:150',
            'category' => 'nullable|in:technical,soft,language,tool',
            'discipline' => 'nullable|in:programming,marketing,healthcare',
            'source' => 'nullable|in:courses,experience',
        ]);
        $skills = Skill::withCount(['jobSkills as active_job_count' => fn ($q) => $q->whereHas('jobPosting', fn ($job) => $job->where('is_active', true)->whereHas('employer.user', fn ($user) => $user->where('is_active', true))->where(fn ($deadline) => $deadline->whereNull('deadline')->orWhere('deadline', '>=', now()->startOfDay())))]);
        if ($q = $input['q'] ?? null) {
            $skills->where(fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%"));
        }
        if ($category = $input['category'] ?? null) {
            $skills->where('category', $category);
        }

        if ($discipline = $input['discipline'] ?? null) {
            $skills->where('discipline', $discipline);
        }
        if ($source = $input['source'] ?? null) {
            $skills->where('source', $source);
        }

        return view('jobmatch.skills', ['skills' => $skills->orderBy('id')->paginate(3)->withQueryString()]);
    }

    public function skill(Skill $skill): View
    {
        return view('jobmatch.skill', compact('skill'));
    }

    public function employers(Request $request): View
    {
        $input = $request->validate([
            'q' => 'nullable|string|max:150',
            'industry' => 'nullable|string|max:80',
            'location' => 'nullable|string|max:100',
            'size' => ['nullable', Rule::enum(CompanySize::class)],
        ]);
        $companies = Employer::where('company_name', '!=', 'Not specified')->whereHas('user', fn ($user) => $user->where('is_active', true))->withCount(['jobPostings' => fn ($job) => $job->where('is_active', true)->where(fn ($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', now()->startOfDay()))]);
        if ($q = $input['q'] ?? null) {
            $companies->where(fn ($company) => $company->where('company_name', 'like', "%{$q}%")->orWhere('industry', 'like', "%{$q}%")->orWhere('location', 'like', "%{$q}%")->orWhere('company_description', 'like', "%{$q}%"));
        }
        if ($industry = $input['industry'] ?? null) {
            $aliases = EmployerIndustry::tryFrom($industry)?->aliases() ?? [$industry];
            $companies->where(function ($query) use ($aliases) {
                foreach ($aliases as $alias) {
                    $query->orWhereRaw('LOWER(industry) = ?', [mb_strtolower($alias)]);
                }
            });
        }

        if ($location = $input['location'] ?? null) {
            $companies->where('location', 'like', "%{$location}%");
        }
        if ($size = $input['size'] ?? null) {
            $companies->whereIn('company_size', CompanySize::from($size)->matchingBuckets());
        }

        return view('jobmatch.employers', ['companies' => $companies->orderBy('company_name')->paginate(8)->withQueryString(), 'industries' => collect(EmployerIndustry::cases())->map(fn ($industry) => $industry->value)->merge(Employer::whereHas('user', fn ($u) => $u->where('is_active', true))->where('industry', '!=', 'Not specified')->distinct()->pluck('industry'))->unique()->sort()->values()]);
    }

    public function employer(Employer $employer): View
    {
        abort_unless($employer->user?->is_active, 404);

        return view('jobmatch.employer', ['company' => $employer, 'jobs' => $this->openJobs()->where('employer_id', $employer->id)->latest('posted_date')->paginate(6)]);
    }

    public function about(): View
    {
        return view('jobmatch.about');
    }

    private function openJobs(): Builder
    {
        return JobPosting::with('employer')->openForDirectory();
    }
}
