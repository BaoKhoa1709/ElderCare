<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uid' => $this->uid,
            'fullName' => $this->full_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'role' => $this->role->value,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
