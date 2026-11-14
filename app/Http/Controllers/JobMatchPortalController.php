<?php

namespace App\Http\Controllers;

use App\Enum\CompanySize;
use App\Enum\UserRole;
use App\Models\Alumni;
use App\Models\Certification;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\PlacementRecord;
use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use App\Services\ProfileCompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobMatchPortalController extends Controller
{
    /**
     * @param  list<string>  $roles
     */
    private function activeUser(Request $request, array $roles = []): User
    {
        $user = $request->user();
        abort_unless($user && $user->is_active, 403);
        abort_if($roles && ! in_array($user->role->value, $roles, true), 403);

        return $user;
    }

    private function ownedStudent(Request $request): Student
    {
        $user = $this->activeUser($request, ['student']);

        return Student::where('user_id', $user->id)->firstOrFail();
    }

    public function dashboard(Request $request): View
    {
        $user = $this->activeUser($request);
        $student = Student::where('user_id', $user->id)->first();
        $company = Employer::where('user_id', $user->id)->first();
        $jobs = JobPosting::with('employer', 'requiredSkills.skill')
            ->openForDirectory()
            ->latest('posted_date')
            ->limit(3)
            ->get();
        $skills = $student?->studentSkills()->with('skill')->limit(5)->get() ?? collect();
        $certifications = $student ? Certification::where('student_id', $student->id)->latest('date_issued')->limit(3)->get() : collect();
        $placements = $student?->placementRecords()->with('jobPosting.employer')->get() ?? collect();
        $skillCount = $student?->studentSkills()->count() ?? 0;
        $certificationCount = $student ? Certification::where('student_id', $student->id)->count() : 0;
        $hasCredentialLink = $student && Certification::where('student_id', $student->id)->whereNotNull('verification_url')->where('verification_url', '!=', '')->exists();
        $completion = app(ProfileCompletionService::class)->calculate($user, $student, $skillCount, (bool) $hasCredentialLink);
        $ownedSkillIds = $student?->studentSkills()->pluck('skill_id')->all() ?? [];
        $companyJobs = $company?->jobPostings()->withCount('placementRecords')->latest('posted_date')->get() ?? collect();

        return view('jobmatch.dashboard', compact('user', 'student', 'company', 'jobs', 'skills', 'certifications', 'placements', 'companyJobs', 'completion', 'skillCount', 'certificationCount', 'ownedSkillIds'));
    }

    public function profile(Request $request): View
    {
        $user = $this->activeUser($request);

        return view('jobmatch.profile', ['user' => $user, 'student' => Student::where('user_id', $user->id)->first(), 'alumni' => Alumni::where('user_id', $user->id)->first()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $this->activeUser($request);
        $student = Student::where('user_id', $user->id)->first();
        $rules = [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];
        if ($user->role === UserRole::STUDENT) {
            $rules += [
                'student_id_number' => ['required', 'string', 'max:30', Rule::unique('students')->ignore($student?->id)],
                'program' => 'required|string|max:100',
                'year_level' => 'required|in:fourth_year,alumni',
                'gwa' => 'required|numeric|between:1,5',
                'graduation_date' => 'nullable|date',
            ];
        }
        if ($user->role === UserRole::ALUMNI) {
            $rules += ['program' => 'required|string|max:100', 'graduation_date' => 'required|date'];
        }
        $data = $request->validate($rules);
        DB::transaction(function () use ($user, $data) {
            $identity = array_intersect_key($data, array_flip(['first_name', 'middle_name', 'last_name', 'email']));
            if ($user->email !== $identity['email']) {
                $identity['email_verified_at'] = null;
            }
            $user->forceFill($identity)->save();
            if ($user->role === UserRole::STUDENT) {
                Student::updateOrCreate(['user_id' => $user->id], array_intersect_key($data, array_flip(['student_id_number', 'program', 'year_level', 'gwa', 'graduation_date'])));
            }
            if ($user->role === UserRole::ALUMNI) {
                Alumni::updateOrCreate(['user_id' => $user->id], ['program' => $data['program'], 'graduation_date' => $data['graduation_date']]);
            }
        });

        return back()->with('status', 'Profile updated.');
    }

    public function resume(Request $request): View
    {
        $user = $this->activeUser($request, ['student']);

        return view('jobmatch.resume', ['student' => Student::where('user_id', $user->id)->first()]);
    }

    public function uploadResume(Request $request): RedirectResponse
    {
        $student = $this->ownedStudent($request);
        $request->validate(['resume' => 'required|file|mimes:pdf|max:5120']);
        $old = $student->resume_file_path;
        $path = $request->file('resume')->store('resumes/'.$student->user_id, 'local');
        try {
            $student->update(['resume_file_path' => $path]);
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($path);
            throw $error;
        }
        if ($old && str_starts_with($old, 'resumes/'.$student->user_id.'/')) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('status', 'Resume uploaded.');
    }

    public function downloadResume(Request $request): StreamedResponse
    {
        $student = $this->ownedStudent($request);
        abort_unless($student->resume_file_path && str_starts_with($student->resume_file_path, 'resumes/'.$student->user_id.'/') && Storage::disk('local')->exists($student->resume_file_path), 404);

        return Storage::disk('local')->download($student->resume_file_path, 'resume.pdf');
    }

    public function skills(Request $request): View
    {
        $user = $this->activeUser($request, ['student']);
        $student = Student::where('user_id', $user->id)->first();

        return view('jobmatch.student-skills', ['student' => $student, 'items' => $student?->studentSkills()->with('skill')->get() ?? collect(), 'skills' => Skill::orderBy('name')->get()]);
    }

    public function saveSkill(Request $request): RedirectResponse
    {
        $student = $this->ownedStudent($request);
        $data = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'proficiency_level' => 'required|integer|between:1,5',
            'years_of_experience' => 'nullable|integer|between:0,60',
        ]);
        $student->studentSkills()->updateOrCreate(['skill_id' => $data['skill_id']], $data);

        return back()->with('status', 'Skill saved.');
    }

    public function deleteSkill(Request $request, int $id): RedirectResponse
    {
        $this->ownedStudent($request)->studentSkills()->findOrFail($id)->delete();

        return back()->with('status', 'Skill removed.');
    }

    public function certifications(Request $request): View
    {
        $user = $this->activeUser($request, ['student']);
        $student = Student::where('user_id', $user->id)->first();

        return view('jobmatch.certifications', ['student' => $student, 'items' => $student ? Certification::where('student_id', $student->id)->latest('date_issued')->get() : collect()]);
    }

    public function saveCertification(Request $request): RedirectResponse
    {
        $student = $this->ownedStudent($request);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'issuing_authority' => 'required|string|max:100',
            'date_issued' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:date_issued',
            'verification_url' => 'nullable|url:http,https|max:500',
            'credential_id' => 'nullable|string|max:50',
        ]);
        Certification::create(['student_id' => $student->id] + $data);

        return back()->with('status', 'Certification saved.');
    }

    public function deleteCertification(Request $request, int $id): RedirectResponse
    {
        Certification::where('student_id', $this->ownedStudent($request)->id)->findOrFail($id)->delete();

        return back()->with('status', 'Certification removed.');
    }

    public function placements(Request $request): View
    {
        $user = $this->activeUser($request, ['student', 'employer', 'admin', 'placement_officer']);
        $input = $request->validate(['status' => 'nullable|in:pending,hired,rejected,offer_accepted,offer_declined,internship']);
        $query = PlacementRecord::with('jobPosting.employer', 'student.user');
        if ($status = $input['status'] ?? null) {
            $query->where('placement_status', $status);
        }
        if ($user->role === UserRole::STUDENT) {
            $query->whereHas('student', fn ($q) => $q->where('user_id', $user->id));
        } elseif ($user->role === UserRole::EMPLOYER) {
            $query->whereHas('jobPosting.employer', fn ($q) => $q->where('user_id', $user->id));
        } elseif (! in_array($user->role, [UserRole::ADMIN, UserRole::PLACEMENT_OFFICER], true)) {
            $query->whereRaw('1 = 0');
        }

        return view('jobmatch.placements', ['items' => $query->latest('placement_date')->paginate(20)->withQueryString()]);
    }

    public function company(Request $request): View
    {
        $user = $this->activeUser($request, ['employer']);

        return view('jobmatch.company', ['company' => Employer::where('user_id', $user->id)->first()]);
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $user = $this->activeUser($request, ['employer']);
        $data = $request->validate([
            'company_name' => 'required|string|max:80',
            'industry' => 'required|string|max:80',
            'location' => 'nullable|string|max:100',
            'company_size' => ['nullable', Rule::enum(CompanySize::class)],
            'website' => 'nullable|url:http,https|max:500',
            'company_description' => 'nullable|string|max:10000',
            'logo_url' => 'required|url:http,https|max:500',
        ]);
        Employer::updateOrCreate(['user_id' => $user->id], $data);

        return back()->with('status', 'Company profile saved.');
    }

    public function jobs(Request $request): View
    {
        $user = $this->activeUser($request, ['employer']);
        $company = Employer::where('user_id', $user->id)->first();
        $jobs = $company?->jobPostings()->latest('posted_date')->get() ?? collect();

        return view('jobmatch.manage-jobs', compact('company', 'jobs'));
    }

    private function jobData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:20000',
            'requirements' => 'required|string|max:10000',
            'preferred_skills' => 'nullable|string|max:5000',
            'min_experience_years' => 'required|integer|between:0,60',
            'min_salary' => 'required|numeric|between:0,9999999.99',
            'max_salary' => 'required|numeric|gte:min_salary|max:9999999.99',
            'location' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,internship,contract,freelance',
            'deadline' => 'required|date|after_or_equal:today',
            'is_active' => 'required|boolean',
        ]);
    }

    public function saveJob(Request $request): RedirectResponse
    {
        $user = $this->activeUser($request, ['employer']);
        $company = Employer::where('user_id', $user->id)->firstOrFail();
        if ($company->company_name === 'Not specified') {
            return back()->withErrors(['company' => 'Complete your company profile before posting a job.']);
        }
        $company->jobPostings()->create($this->jobData($request) + ['posted_date' => now()]);

        return back()->with('status', 'Job posting created.');
    }

    public function updateJob(Request $request, JobPosting $job): RedirectResponse
    {
        $user = $this->activeUser($request, ['employer']);
        abort_unless($job->employer?->user_id === $user->id, 403);
        $job->update($this->jobData($request));

        return back()->with('status', 'Job posting updated.');
    }

    public function reports(Request $request): StreamedResponse
    {
        $this->activeUser($request, ['admin', 'placement_officer']);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student', 'Job', 'Employer', 'Status', 'Placement date'], escape: '');
            PlacementRecord::with('student.user', 'jobPosting.employer')->orderBy('id')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    $values = [$row->student?->user?->full_name, $row->jobPosting?->title, $row->jobPosting?->employer?->company_name, $row->placement_status->value, $row->placement_date?->toDateString()];
                    fputcsv($out, array_map(fn ($v) => preg_match('/^[=+@\-\t\r]/', (string) $v) ? "'".$v : $v, $values), escape: '');
                }
            });
            fclose($out);
        }, 'jobmatch-placement-report.csv', ['Content-Type' => 'text/csv']);
    }
}
