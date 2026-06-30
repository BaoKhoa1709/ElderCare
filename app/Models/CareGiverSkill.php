<?php

namespace App\Models;

use App\Enums\CareNeed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareGiverSkill extends Model
{
    protected $fillable = [
        'care_giver_uid',
        'skill_name',
    ];

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }

    protected function casts(): array
    {
        return [
            'skill_name' => CareNeed::class,
        ];
    }
}
