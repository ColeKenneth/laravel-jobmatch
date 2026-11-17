<?php

use App\Http\Controllers\JobMatchPortalController as Portal;
use App\Http\Controllers\JobMatchPublicController as PublicPages;
use Illuminate\Support\Facades\Route;

// Public directories can be browsed without an account.
Route::get('/', [PublicPages::class, 'home'])->name('home');
Route::get('/jobs', [PublicPages::class, 'jobs'])->name('jobs.index');
Route::get('/jobs/{job}', [PublicPages::class, 'job'])->name('jobs.show');
Route::get('/skills', [PublicPages::class, 'skills'])->name('skills.index');
Route::get('/skills/{skill}', [PublicPages::class, 'skill'])->name('skills.show');
Route::get('/employers', [PublicPages::class, 'employers'])->name('employers.index');
Route::get('/employers/{employer}', [PublicPages::class, 'employer'])->name('employers.show');
Route::get('/about', [PublicPages::class, 'about'])->name('about');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'role:student,alumni,employer,admin,placement_officer',
])->group(function () {
    // Shared account pages.
    Route::get('/dashboard', [Portal::class, 'dashboard'])->name('dashboard');
    Route::get('/portal/profile', [Portal::class, 'profile'])->name('portal.profile');
    Route::put('/portal/profile', [Portal::class, 'updateProfile'])->name('portal.profile.update');

    // Student career records.
    Route::middleware('role:student')->group(function () {
        Route::get('/portal/resume', [Portal::class, 'resume'])->name('portal.resume');
        Route::post('/portal/resume', [Portal::class, 'uploadResume'])->name('portal.resume.upload');
        Route::get('/portal/resume/download', [Portal::class, 'downloadResume'])->name('portal.resume.download');
        Route::get('/portal/skills', [Portal::class, 'skills'])->name('portal.skills');
        Route::post('/portal/skills', [Portal::class, 'saveSkill'])->name('portal.skills.save');
        Route::delete('/portal/skills/{id}', [Portal::class, 'deleteSkill'])->name('portal.skills.delete');
        Route::get('/portal/certifications', [Portal::class, 'certifications'])->name('portal.certifications');
        Route::post('/portal/certifications', [Portal::class, 'saveCertification'])->name('portal.certifications.save');
        Route::delete('/portal/certifications/{id}', [Portal::class, 'deleteCertification'])->name('portal.certifications.delete');
    });

    // Employer management; controllers also enforce record ownership.
    Route::middleware('role:employer')->group(function () {
        Route::get('/portal/company', [Portal::class, 'company'])->name('portal.company');
        Route::put('/portal/company', [Portal::class, 'updateCompany'])->name('portal.company.update');
        Route::get('/portal/jobs', [Portal::class, 'jobs'])->name('portal.jobs');
        Route::post('/portal/jobs', [Portal::class, 'saveJob'])->name('portal.jobs.save');
        Route::put('/portal/jobs/{job}', [Portal::class, 'updateJob'])->name('portal.jobs.update');
    });

    // Placement visibility is scoped by the controller.
    Route::middleware('role:student,employer,admin,placement_officer')->group(function () {
        Route::get('/portal/placements', [Portal::class, 'placements'])->name('portal.placements');
    });

    // Staff reports.
    Route::middleware('role:admin,placement_officer')->group(function () {
        Route::get('/portal/reports', [Portal::class, 'reports'])->name('portal.reports');
    });
});
