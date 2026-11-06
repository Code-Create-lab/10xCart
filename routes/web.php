<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home.index')->name('home');

// Portfolio for clients: shared by link only (not linked from the site, noindex). Content: config/work.php
Route::view('/work', 'pages.work.index')->name('work');

// Contact form (max 5 submissions per minute per visitor)
Route::post('/contact', [LeadController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('lead.store');

// Visitor tracking beacon: stores nothing unless analytics cookies were accepted
// (CSRF-exempt in bootstrap/app.php because navigator.sendBeacon cannot send the token)
Route::post('/t', [TrackingController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('track');

/*
| Admin panel (/admin). Create a login with: php artisan admin:create you@example.com
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
        Route::get('/visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
        Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
    });
});
