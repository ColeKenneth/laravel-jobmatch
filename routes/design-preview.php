<?php

use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    Route::redirect('/design-preview/register', '/register')->name('design-preview.register');
    Route::redirect('/design-preview/forgot', '/forgot-password')->name('design-preview.forgot');
    foreach (['home', 'jobs', 'skills', 'employers', 'employer', 'about', 'job'] as $screen) {
        Route::view('/design-preview/'.$screen, 'design-preview.'.$screen)
            ->name('design-preview.'.$screen);
    }
}
