<?php

use App\Http\Controllers\BookingsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'loginPage'])->name('login');

Route::post('/login', [LoginController::class, 'loginAction']);

Route::get('/events/closest_main', [EventsController::class, 'closestMain']);

Route::get('/bookings/confirmation', [BookingsController::class, 'confirmation'])
    ->name('bookings.confirmation')
    ->middleware('signed');
