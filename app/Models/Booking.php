<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\CareLocation;
use App\Enums\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'care_location',
        'from_date',
        'duration',
        'status',
        'start_time',
        'end_time',
        'care_seeker_uid',
        'care_giver_uid',
        'note',
        'meeting_link',
        'payment',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'status' => BookingStatus::class,
            'care_location' => CareLocation::class,
            'payment' => Payment::class,
        ];
    }

    public function careSeeker(): BelongsTo
    {
        return $this->belongsTo(CareSeeker::class, 'care_seeker_uid', 'uid');
    }

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
