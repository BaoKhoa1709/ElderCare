<?php

namespace App\Services;

use App\Dto\BookingDto;
use App\Enums\BookingStatus;

interface BookingService
{
    public function create(array $data): BookingDto;

    public function getByUid(string $uid): ?BookingDto;

    public function getAll(): array;

    public function updateStatus(string $uid, BookingStatus $status): BookingDto;

    public function delete(string $uid): bool;
}
