<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRecommendation extends Model
{
    protected $fillable = [
        'care_seeker_uid',
        'care_giver_uid',
        'match_point',
    ];

    public function careSeeker(): BelongsTo
    {
        return $this->belongsTo(CareSeeker::class, 'care_seeker_uid', 'uid');
    }

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }
}