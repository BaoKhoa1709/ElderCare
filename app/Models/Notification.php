<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'care_seeker_uid',
        'care_giver_uid',
        'match_point',
        'message',
        'type',
        'is_read',
        'user_uid',
    ];

    public function careSeeker(): BelongsTo
    {
        return $this->belongsTo(CareSeeker::class, 'care_seeker_uid', 'uid');
    }

    public function careGiver(): BelongsTo
    {
        return $this->belongsTo(CareGiver::class, 'care_giver_uid', 'uid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'type' => NotificationType::class,
        ];
    }
}
