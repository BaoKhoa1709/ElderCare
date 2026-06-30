<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareGiverResource extends JsonResource
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
            'yearExperience' => $this->yearExperience,
            'fee' => $this->fee,
            'bio' => $this->bio,
            'imageUrl' => $this->imageUrl,
            'skills' => $this->skills,
            'certifications' => $this->certifications,
            'schedules' => $this->schedules,
        ];
    }
}