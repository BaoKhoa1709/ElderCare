<?php

namespace App\Services;

use App\Dto\CareGiverDto;
use App\Dto\PageableDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CareGiverService
{
    public function createCareGiver(array $data): CareGiverDto;

    public function getByUid(string $uid): ?CareGiverDto;

    public function getAll(): array;

    public function deleteByUid(string $uid): bool;

    public function searchByName(string $name): array;

    public function searchByNamePaginated(string $name, PageableDto $pageable): LengthAwarePaginator;

    public function linkImageToGiver(string $giverUid, string $filePath): string;
}
