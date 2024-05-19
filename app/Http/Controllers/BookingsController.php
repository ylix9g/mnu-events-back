<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Sector;
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
            'sector_id' => ['exists:sectors,id'],
            'email' => ['required', 'email', new EndsWithRule(Booking::EMAIL_ENDING)],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ]);
        $event = Event::findOrFail($validated['event_id']);
        $sector = null;
        if ($validated['sector_id']) {
            $sector = Sector::findOrFail($validated['sector_id']);
            if ($sector->event->id !== $event->id) {
                abort(404);
            }
        }
        $booking = new Booking();
        $booking->event()->associate($event);
        if ($sector) {
            $booking->sector()->associate($sector);
        }
        $booking->email = $validated['email'];
        $booking->first_name = $validated['first_name'];
        $booking->last_name = $validated['last_name'];
        $booking->save();
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
