<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'main',
        'name',
        'description',
        'location',
        'limit',
        'start',
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
        return $this->bookings()
            ->where('confirmed', false)
            ->where('created_at', '>=', now()->subMinutes(Booking::MINUTES_FOR_CONFIRMATION))
            ->count();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    protected function casts(): array
    {
        return [
            'main' => 'bool',
            'start' => 'datetime',
        ];
    }
}
