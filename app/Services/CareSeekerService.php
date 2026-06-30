<?php

namespace App\Services;

use App\Dto\CareSeekerDto;

interface CareSeekerService
{
    public function createCareSeeker(array $data): CareSeekerDto;
    public function getAll(): array;
    public function getById(string $uid): ?CareSeekerDto;
    public function deleteById(string $uid): bool;
}
