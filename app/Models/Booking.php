<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const MINUTES_FOR_CONFIRMATION = 20;

    public const EMAIL_ENDING = '@kazguu.kz';

    protected $fillable = [
        'confirmed',
        'email',
        'first_name',
        'last_name',
    ];

    public function event() : BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts()
    {
        return [
            'confirmed' => 'bool',
        ];
    }
}
