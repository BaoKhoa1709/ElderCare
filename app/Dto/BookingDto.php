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
        public string $type,
        public ?string $careGiverName,
        public ?string $careGiverEmail,
        public ?string $careGiverPhone,
        public ?string $careSeekerName,
        public ?string $careSeekerEmail,
        public ?string $careSeekerPhone,
    ) {}

    public static function fromArray(array $data): self
    {
        $seeker = $data['care_seeker'] ?? null;
        $giver = $data['care_giver'] ?? null;

        $careSeekerName = null;
        $careSeekerEmail = null;
        $careSeekerPhone = null;
        if ($seeker) {
            $careSeekerPhone = $seeker->phone_number ?? null;
            if ($seeker->user) {
                $careSeekerName = $seeker->user->full_name ?? null;
                $careSeekerEmail = $seeker->user->email ?? null;
            }
        }

        $careGiverName = null;
        $careGiverEmail = null;
        $careGiverPhone = null;
        if ($giver) {
            $careGiverPhone = $giver->phone_number ?? null;
            if ($giver->user) {
                $careGiverName = $giver->user->full_name ?? null;
                $careGiverEmail = $giver->user->email ?? null;
            }
        }

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
            type: $data['type'] ?? '',
            careGiverName: $careGiverName,
            careGiverEmail: $careGiverEmail,
            careGiverPhone: $careGiverPhone,
            careSeekerName: $careSeekerName,
            careSeekerEmail: $careSeekerEmail,
            careSeekerPhone: $careSeekerPhone,
        );
    }
}