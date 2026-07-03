<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'task_name',
        'type',
        'booking_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaskType::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}