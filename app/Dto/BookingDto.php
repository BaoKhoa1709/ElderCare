<?php

namespace App\Dto;

class BookingDto
{
    public function __construct(
        public int $id,
        public string $careLocation,
        public ?string $fromDate,
        public int $duration,
        public string $status,
        public ?string $startTime,
        public ?string $endTime,
        public string $careSeekerUid,
        public string $careGiverUid,
        public ?string $note,
        public ?string $meetingLink,
        public string $payment,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            careLocation: $data['care_location'],
            fromDate: $data['from_date'] ?? null,
            duration: $data['duration'],
            status: $data['status'],
            startTime: $data['start_time'] ?? null,
            endTime: $data['end_time'] ?? null,
            careSeekerUid: $data['care_seeker_uid'],
            careGiverUid: $data['care_giver_uid'],
            note: $data['note'] ?? null,
            meetingLink: $data['meeting_link'] ?? null,
            payment: $data['payment'],
        );
    }
}
