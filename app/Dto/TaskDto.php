<?php

namespace App\Dto;

use App\Enums\TaskType;

class TaskDto
{
    public function __construct(
        public int $id,
        public string $taskName,
        public TaskType $type,
        public int $bookingId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            taskName: $data['task_name'],
            type: TaskType::from($data['type']),
            bookingId: $data['booking_id']
        );
    }
}
