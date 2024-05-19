<?php

use App\Http\Controllers\BookingsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FilesController;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/events/grouped_by_category', [EventsController::class, 'groupedByCategory']);

Route::get('/bookings/book_for_event', [BookingsController::class, 'bookForEvent']);

Route::middleware('auth:api')->group(function () {

    Route::post('/files/upload', [FilesController::class, 'upload']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/categories', [CategoriesController::class, 'categories']);

    Route::post('/events/create', [EventsController::class, 'createEvent']);

    Route::get('/events/by_date', [EventsController::class, 'byDate']);

});

Route::get('/events/{event}', function (Event $event) {
    return response()->json($event);
});
