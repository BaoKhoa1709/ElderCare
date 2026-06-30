<?php

namespace App\Services;

use App\Dto\CareGiverDto;

interface CareGiverService
{
    public function createCareGiver(array $data): CareGiverDto;
    public function getByUid(string $uid): ?CareGiverDto;
    public function getAll(): array;
    public function deleteByUid(string $uid): bool;
    public function searchByName(string $name): array;
}
