<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareGiver extends Model
{
    protected $primaryKey = 'uid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uid',
        'user_uid',
        'dob',
        'phone_number',
        'year_experience',
        'fee',
        'bio',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CareGiverSkill::class, 'care_giver_uid', 'uid');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(CaregiverCertification::class, 'care_giver_uid', 'uid');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CaregiverSchedule::class, 'care_giver_uid', 'uid');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'care_giver_uid', 'uid');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'care_giver_uid', 'uid');
    }
}
