<?php

namespace App\Dto;

class SkillDto
{
    public function __construct(
        public string $skillName
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(skillName: $data['skill_name']);
    }

    public function toArray(): array
    {
        return ['skill_name' => $this->skillName];
    }
}