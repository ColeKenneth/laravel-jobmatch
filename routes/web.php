<?php

use App\Livewire\AlumniList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['role:admin'])->prefix('admin')->group(function () {
    Route::get('/alumni', AlumniList::class)->name('admin.alumni');
});
