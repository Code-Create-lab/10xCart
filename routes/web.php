<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home.index')->name('home');

// Contact form (max 5 submissions per minute per visitor)
Route::post('/contact', [LeadController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('lead.store');
