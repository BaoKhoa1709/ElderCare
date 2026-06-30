<?php

namespace App\Dto;

class CareSeekerDto
{
    public function __construct(
        public string $uid,
        public string $userUid,
        public ?string $dob,
        public ?string $phoneNumber,
        public ?string $preferredGiverGender
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            uid: $data['uid'],
            userUid: $data['user_uid'],
            dob: $data['dob'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            preferredGiverGender: $data['preferred_giver_gender'] ?? null
        );
    }
}
