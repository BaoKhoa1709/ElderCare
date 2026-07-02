<?php

namespace App\Services;

interface NotificationService
{
    public function getNotifications(string $careSeekerUid): array;

    public function generateMatches(string $careSeekerUid): array;

    public function findMatchesForCareSeeker(string $careSeekerUid): array;

    public function getAllForUser(User $user): array;
}
