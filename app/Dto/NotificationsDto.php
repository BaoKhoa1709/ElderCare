<?php

namespace App\Dto;

use App\Models\CareGiver;

class NotificationsDto
{
    public function __construct(
        public string $message,
        public string $type,
        public ?int $id = null,
        public bool $isRead = false,
        public ?string $createdAt = null,
        public mixed $payload = null,
    ) {}

    public static function fromMatch(CareGiver $careGiver, ?int $id = null, bool $isRead = false, ?string $createdAt = null): self
    {
        return new self(
            message: 'Tìm thấy CareGiver phù hợp cho bạn',
            type: 'MATCH_FOUND',
            id: $id,
            isRead: $isRead,
            createdAt: $createdAt,
            payload: [
                'uid' => $careGiver->uid,
                'fullName' => $careGiver->user->full_name,
                'gender' => $careGiver->user->gender?->value,
                'imageUrl' => $careGiver->image_url,
                'yearExperience' => $careGiver->year_experience,
                'fee' => $careGiver->fee,
            ],
        );
    }

    public static function fromType(string $type, string $message, ?int $id = null, bool $isRead = false, ?string $createdAt = null): self
    {
        return new self(
            message: $message,
            type: $type,
            id: $id,
            isRead: $isRead,
            createdAt: $createdAt,
            payload: null,
        );
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'id' => $this->id,
            'isRead' => $this->isRead,
            'createdAt' => $this->createdAt,
            'payload' => $this->payload,
        ];
    }
}
