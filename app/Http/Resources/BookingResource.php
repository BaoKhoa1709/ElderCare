<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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
            'type' => $this->type,
            'careGiverName' => $this->careGiverName,
            'careGiverEmail' => $this->careGiverEmail,
            'careGiverPhone' => $this->careGiverPhone,
            'careSeekerName' => $this->careSeekerName,
            'careSeekerEmail' => $this->careSeekerEmail,
            'careSeekerPhone' => $this->careSeekerPhone,
        ];
    }
}
