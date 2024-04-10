<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Event;
use App\Rules\EndsWithRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class BookingsController extends Controller
{
    public function bookForEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'email' => ['required', 'email', new EndsWithRule(Booking::EMAIL_ENDING)],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ]);
        $event = Event::findOrFail($validated['event_id']);
        $booking = $event->bookings()->create([
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
        ]);
        $confirmationExpiration = $booking->created_at->addMinutes(Booking::MINUTES_FOR_CONFIRMATION);
        $confirmationUrl = URL::temporarySignedRoute('bookings.confirmation', $confirmationExpiration, [
            'booking_id' => $booking->id,
        ]);
        Mail::to($booking->email)->send(new BookingConfirmation($booking, $confirmationUrl));
        return response()->json();
    }

    public function confirmation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
        ]);
        $booking = Booking::findOrFail($validated['booking_id']);
        $booking->confirmed = true;
        $booking->save();
        return response()->json('success');
    }
}
