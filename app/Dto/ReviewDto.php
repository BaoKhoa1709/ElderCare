<?php

namespace App\Dto;

class ReviewDto
{
    public function __construct(
        public int $id,
        public int $bookingId,
        public int $rating,
        public string $comment,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            bookingId: $data['booking_id'],
            rating: $data['rating'],
            comment: $data['comment'],
        );
    }
}
