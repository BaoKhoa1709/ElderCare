<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'taskName' => $this->taskName,
            'type' => $this->type->value,
            'bookingId' => $this->bookingId,
        ];
    }
}