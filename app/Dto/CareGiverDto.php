<?php

namespace App\Dto;

class CareGiverDto
{
    public function __construct(
        public string $uid,
        public string $userUid,
        public ?string $dob,
        public ?string $phoneNumber,
        public ?int $yearExperience,
        public ?float $fee,
        public ?string $bio,
        public ?string $imageUrl
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            uid: $data['uid'],
            userUid: $data['user_uid'],
            dob: $data['dob'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            yearExperience: $data['year_experience'] ?? null,
            fee: $data['fee'] ?? null,
            bio: $data['bio'] ?? null,
            imageUrl: $data['image_url'] ?? null
        );
    }
}
