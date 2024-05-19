<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'limit',
    ];

    protected $appends = [
        'confirmed_bookings_count',
        'unconfirmed_bookings_count',
    ];

    public function getConfirmedBookingsCountAttribute(): int
    {
        return $this->bookings()->where('confirmed', true)->count();
    }

    public function getUnconfirmedBookingsCountAttribute(): int
    {
        $deadline = now()->subMinutes(Booking::MINUTES_FOR_CONFIRMATION);
        return $this->bookings()
            ->where('confirmed', false)
            ->where('created_at', '>=', $deadline)
            ->count();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
