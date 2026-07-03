<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaregiverSchedule extends Model
{
    protected $fillable = [
        'care_giver_uid',
        'day_of_weeks',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'day_of_weeks' => 'array',
        ];
    }

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }
}
