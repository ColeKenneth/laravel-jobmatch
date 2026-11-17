<?php

use App\Livewire\Alumni;
use Illuminate\Support\Facades\Route;

// The admin area is restricted to active administrators, including direct URL access.
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'role:admin',
])->prefix('admin')->group(function () {
    Route::get('/alumni', Alumni::class)->name('admin.alumni');
});

require __DIR__.'/jobmatch.php';

// Redirect legacy URLs to the current pages.
foreach (['home' => '/', 'jobs' => '/jobs', 'skills' => '/skills', 'employers' => '/employers', 'about' => '/about', 'register' => '/register', 'forgot' => '/forgot-password'] as $old => $current) {
    Route::redirect('/design-preview/'.$old, $current);
}
Route::redirect('/design-preview', '/');
