<?php

namespace App\Dto;

class ScheduleDto
{
    public function __construct(
        public array $days,
        public ?string $startTime = null,
        public ?string $endTime = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            days: $data['day_of_weeks'] ?? [],
            startTime: $data['start_time'] ?? null,
            endTime: $data['end_time'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'day_of_weeks' => $this->days,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }
}