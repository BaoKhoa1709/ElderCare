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
        public ?string $imageUrl,
        public ?array $skills = null,
        public ?array $certifications = null,
        public ?array $schedules = null,
        public ?string $fullName = null,
        public ?string $email = null
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
            imageUrl: $data['image_url'] ?? null,
            skills: $data['skills'] ?? null,
            certifications: $data['certifications'] ?? null,
            schedules: $data['schedules'] ?? null,
            fullName: $data['full_name'] ?? null,
            email: $data['email'] ?? null
        );
    }
}