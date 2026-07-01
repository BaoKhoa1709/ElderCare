<?php

namespace App\Dto;

use App\Enums\CareNeed;
use App\Enums\HealthCondition;

class CareSeekerDto
{
    public function __construct(
        public string $uid,
        public string $userUid,
        public ?string $dob,
        public ?string $phoneNumber,
        public ?string $preferredGiverGender,
        public ?array $careNeeds = null,
        public ?array $healthConditions = null,
        public ?string $fullName = null,
        public ?string $email = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uid: $data['uid'],
            userUid: $data['user_uid'],
            dob: $data['dob'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            preferredGiverGender: $data['preferred_giver_gender'] ?? null,
            careNeeds: isset($data['care_needs'])
                ? array_map(fn ($v) => $v instanceof CareNeed ? $v->value : $v, $data['care_needs'])
                : null,
            healthConditions: isset($data['health_conditions'])
                ? array_map(fn ($v) => $v instanceof HealthCondition ? $v->value : $v, $data['health_conditions'])
                : null,
            fullName: $data['full_name'] ?? null,
            email: $data['email'] ?? null
        );
    }
}
