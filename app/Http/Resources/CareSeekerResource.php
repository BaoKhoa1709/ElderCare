<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareSeekerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uid' => $this->uid,
            'userUid' => $this->userUid,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'dob' => $this->dob,
            'phoneNumber' => $this->phoneNumber,
            'preferredGiverGender' => $this->preferredGiverGender,
            'careNeeds' => $this->careNeeds,
            'healthConditions' => $this->healthConditions,
        ];
    }
}
