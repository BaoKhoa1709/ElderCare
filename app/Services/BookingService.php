<?php

namespace App\Services;

use App\Dto\BookingDto;
use App\Enums\BookingStatus;
use App\Models\User;

interface BookingService
{
    public function create(array $data): BookingDto;

    public function getById(int $id): ?BookingDto;

    public function getAllByRole(User $user): array;

    public function updateStatus(int $id, BookingStatus $status): BookingDto;

    public function delete(int $id): bool;

    public function decide(int $bookingId, string $type, ?string $meetingLink, User $user): string;
}
