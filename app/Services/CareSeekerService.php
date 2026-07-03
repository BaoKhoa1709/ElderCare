<?php

namespace App\Services;

use App\Dto\CareSeekerDto;
use App\Models\User;

interface CareSeekerService
{
    public function createCareSeeker(array $data): CareSeekerDto;
    public function getAll(): array;
    public function getById(User $authUser, string $seekerUid): CareSeekerDto;
    public function deleteById(string $uid): bool;
}
