<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uid' => $this->uid,
            'careLocation' => $this->careLocation,
            'fromDate' => $this->fromDate,
            'duration' => $this->duration,
            'status' => $this->status,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'careSeekerUid' => $this->careSeekerUid,
            'careGiverUid' => $this->careGiverUid,
            'note' => $this->note,
            'meetingLink' => $this->meetingLink,
            'payment' => $this->payment,
        ];
    }
}
