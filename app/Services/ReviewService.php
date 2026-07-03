<?php

namespace App\Services;

use App\Dto\ReviewDto;
use App\Models\User;

interface ReviewService
{
    public function create(User $user, array $data): ReviewDto;

    public function getByCareGiverUid(string $careGiverUid): array;

    public function update(User $user, int $reviewId, array $data): ReviewDto;

    public function delete(User $user, int $reviewId): string;
}
