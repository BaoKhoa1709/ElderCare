<?php

namespace App\Models;

use App\Enums\CareNeed;
use App\Enums\Gender;
use App\Enums\HealthCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareSeeker extends Model
{
    protected $primaryKey = 'uid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uid',
        'user_uid',
        'dob',
        'phone_number',
        'preferred_giver_gender',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'preferred_giver_gender' => Gender::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    public function careNeedRecords(): HasMany
    {
        return $this->hasMany(CareSeekerCareNeed::class, 'care_seeker_uid', 'uid');
    }

    public function healthConditionRecords(): HasMany
    {
        return $this->hasMany(CareSeekerHealthCondition::class, 'care_seeker_uid', 'uid');
    }

    public function getCareNeedsAttribute()
    {
        return $this->careNeedRecords->pluck('care_need')->map(fn($v) => CareNeed::from($v));
    }

    public function getHealthConditionsAttribute()
    {
        return $this->healthConditionRecords->pluck('health_condition')->map(fn($v) => HealthCondition::from($v));
    }
}
