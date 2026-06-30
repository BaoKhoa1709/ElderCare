<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareGiverSkill extends Model
{
    protected $table = 'caregiver_skills';

    protected $fillable = [
        'care_giver_uid',
        'skill_name',
    ];

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }
}
