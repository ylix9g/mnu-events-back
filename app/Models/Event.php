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
        'with_sectors',
        'image',
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
        $deadline = now()->subMinutes(Booking::MINUTES_FOR_CONFIRMATION);
        return $this->bookings()
            ->where('confirmed', false)
            ->where('created_at', '>=', $deadline)
            ->count();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sectors(): HasMany
    {
        return $this->hasMany(Sector::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function hasAvailableLimit(Sector $sector = null): bool
    {
        if ($this->with_sectors && !$sector) {
            abort(404);
        }
        $deadline = now()->subMinutes(Booking::MINUTES_FOR_CONFIRMATION);
        if ($this->with_sectors && $sector) {
            $availableLimit = $sector->bookings()
                ->where('confirmed', false)
                ->where('created_at', '<', $deadline)
                ->count();
            return $availableLimit > 0;
        } else {
            $availableLimit = $this->bookings()
                ->where('confirmed', false)
                ->where('created_at', '<', $deadline)
                ->count();
            return $availableLimit > 0;
        }
    }

    protected function casts(): array
    {
        return [
            'main' => 'bool',
            'with_sectors' => 'bool',
            'start' => 'datetime',
        ];
    }
}
