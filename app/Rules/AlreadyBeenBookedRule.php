<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class AlreadyBeenBookedRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $eventId = request()->event_id;
        if (isset($eventId)) {
            $exists = DB::table('bookings')
                ->where('email', $value)
                ->where('event_id', $eventId)
                ->exists();
            if ($exists) {
                $fail('There is already a booking for this event.');
            }
        }
    }
}
