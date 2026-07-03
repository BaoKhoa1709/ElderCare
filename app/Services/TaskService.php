<?php

namespace App\Services;

use App\Dto\TaskDto;

interface TaskService
{
    public function create(User $user, array $data): TaskDto;

    public function getById(int $taskId): ?TaskDto;

    public function getAll(User $user): array;

    public function getAllByBooking(int $bookingId, User $user): array;

    public function update(User $user, int $taskId, array $data): TaskDto;

    public function delete(User $user, int $taskId): string;
}
