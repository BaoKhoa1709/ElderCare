<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareSeekerHealthCondition extends Model
{
    protected $fillable = [
        'care_seeker_uid',
        'health_condition',
    ];

    public function careSeeker(): BelongsTo
    {
        return $this->belongsTo(CareSeeker::class, 'care_seeker_uid', 'uid');
    }
}
