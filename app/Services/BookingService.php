<?php

namespace App\Services;

use App\Dto\BookingDto;
use App\Enums\BookingStatus;

interface BookingService
{
    public function create(array $data): BookingDto;

    public function getById(int $id): ?BookingDto;

    public function getAll(): array;

    public function updateStatus(int $id, BookingStatus $status): BookingDto;

    public function delete(int $id): bool;

    public function decide(int $bookingId, string $type, ?string $meetingLink, User $user): string;
}
