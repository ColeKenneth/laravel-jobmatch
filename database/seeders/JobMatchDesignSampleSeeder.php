<?php

namespace Database\Seeders;

use App\Enum\UserRole;
use App\Models\Employer;
use App\Models\JobSkill;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;

/** Optional local design fixtures, never live vacancies or personal student records. */
class JobMatchDesignSampleSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new LogicException('Design sample records are restricted to APP_ENV=local.');
        }
        DB::transaction(function () {
            $companies = [];
            $definitions = [
                ['acme-health', 'ACME Health Systems', 'Healthcare', 'Quezon City', '51-200', 'Supporting healthcare professionals.', 'acme-health.png'],
                ['innovate', 'Innovate Software Solutions', 'IT & Software', 'Makati', '51-200', 'Developing software solutions for global clients.', 'innovate.png'],
                ['global-logistics', 'Global Logistics Group', 'Logistics', 'Manila', '201-500', 'Connecting products to people.', 'global-logistics.png'],
                ['edutech', 'Edu-Tech Partners', 'Education', 'Quezon City', '1-50', 'Building the future of e-learning.', 'edutech.png'],
                ['acme-tech', 'Acme Tech', 'IT & Software', 'Quezon City', '1-50', 'Technology and data careers.', 'acme-tech.png'],
                ['globe', 'Globe Telecom', 'Telecommunications', 'Makati', '500+', 'Marketing and communications careers.', 'globe.png'],
                ['wcc-innovation', 'WCC Innovation Lab', 'IT & Software', 'Quezon City', '1-50', 'Design and innovation opportunities.', 'wcc-logo.png'],
                ['developer', 'Developer', 'IT & Software', 'Cebu', '1-50', 'Network and software opportunities.', 'innovate.png'],
                ['innovate-labs', 'Innovate Tech Labs', 'IT & Software', 'Manila', '51-200', 'Data and technology opportunities.', 'innovate.png'],
                ['global-ai', 'Global Corp AI', 'IT & Software', 'Davao', '51-200', 'AI and research opportunities.', 'skill-ai.png'],
            ];
            foreach ($definitions as [$key, $name, $industry, $location, $size, $description, $icon]) {
                $user = User::firstOrCreate(['email' => 'design.'.$key.'@jobmatch.example.test'], ['first_name' => 'Design', 'last_name' => 'Sample', 'role' => UserRole::EMPLOYER, 'is_active' => true, 'password' => Hash::make(Str::random(48))]);
                if ($user->role !== UserRole::EMPLOYER) {
                    throw new LogicException('The sample address is already used by another account type.');
                }
                $company = Employer::firstOrCreate(['user_id' => $user->id], ['company_name' => $name, 'industry' => $industry, 'location' => $location, 'company_size' => $size, 'company_description' => $description, 'logo_url' => '/images/jobmatch/current/'.$icon]);
                if ($company->company_name === 'Not specified') {
                    $company->update(['company_name' => $name, 'industry' => $industry, 'location' => $location, 'company_size' => $size, 'company_description' => $description, 'logo_url' => '/images/jobmatch/current/'.$icon]);
                }
                $companies[$key] = $company;
            }
            $skills = [];
            foreach ([
                ['Artificial Intelligence', 'technical', 'programming', 'courses', 'Understand core AI concepts and algorithms.', 'skill-ai.png'],
                ['Project Management', 'technical', 'marketing', 'experience', 'Plan, coordinate, and execute complex projects.', 'skill-project.png'],
                ['Public Speaking', 'soft', 'marketing', 'experience', 'Deliver impactful speeches and presentations.', 'skill-speaking.png'],
                ['Python', 'technical', 'programming', 'courses', 'Build applications and work with data using Python.', 'skill-ai.png'],
                ['SQL', 'technical', 'programming', 'courses', 'Query and manage relational databases.', 'skill-project.png'],
                ['Machine Learning', 'technical', 'programming', 'courses', 'Explore patterns and predictive models.', 'skill-ai.png'],
            ] as [$name, $category, $discipline, $source, $description, $icon]) {
                $skills[$name] = Skill::firstOrCreate(['name' => $name], compact('category', 'discipline', 'source', 'description') + ['icon_url' => '/images/jobmatch/current/'.$icon]);
            }
            $definitions = [
                ['acme-tech', 'Data Analyst', 'full_time', 0, ['SQL', 'Python']],
                ['globe', 'Marketing Coordinator', 'part_time', 2, ['Project Management', 'Public Speaking']],
                ['wcc-innovation', 'UX Designer', 'internship', 0, ['Project Management']],
                ['developer', 'Network Administrator', 'full_time', 1, ['Python', 'SQL']],
                ['innovate-labs', 'Data Analyst', 'full_time', 2, ['SQL']],
                ['global-ai', 'AI Researcher', 'full_time', 4, ['Artificial Intelligence', 'Machine Learning']],
            ];
            foreach ($definitions as $index => [$companyKey, $title, $type, $experience, $required]) {
                $company = $companies[$companyKey];
                $job = $company->jobPostings()->firstOrCreate(['title' => $title], ['description' => 'Local design sample: '.$title.' at '.$company->company_name.'. Replace with an actual job description before any live use.', 'requirements' => 'Relevant education and the skills listed below.', 'preferred_skills' => implode(', ', $required), 'min_experience_years' => $experience, 'min_salary' => null, 'max_salary' => null, 'location' => $company->location ?? 'Location, City', 'employment_type' => $type, 'posted_date' => now()->subHours($index), 'deadline' => now()->addMonths(3), 'is_active' => true]);
                foreach ($required as $skillName) {
                    JobSkill::firstOrCreate(['job_posting_id' => $job->id, 'skill_id' => $skills[$skillName]->id], ['is_required' => true, 'min_proficiency_level' => 3, 'priority' => 1]);
                }
            }
        });
    }
}
