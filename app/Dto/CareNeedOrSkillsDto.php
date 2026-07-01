<?php

namespace App\Dto;

use App\Enums\CareNeed;

class CareNeedOrSkillsDto
{
    public function __construct(
        public array $careNeedOrSkills = []
    ) {}

    public static function fromCareNeedEnums(array $careNeeds): self
    {
        return new self(careNeedOrSkills: array_map(fn($cn) => $cn->value, $careNeeds));
    }
}