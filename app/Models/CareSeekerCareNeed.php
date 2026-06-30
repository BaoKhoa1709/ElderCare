<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareSeekerCareNeed extends Model
{
    protected $fillable = [
        'care_seeker_uid',
        'care_need',
    ];

    public function careSeeker(): BelongsTo
    {
        return $this->belongsTo(CareSeeker::class, 'care_seeker_uid', 'uid');
    }
}
