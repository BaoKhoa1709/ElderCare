<?php

namespace App\Services;

interface NotificationService
{
    public function getNotifications(string $careSeekerUid): array;

    public function generateMatches(string $careSeekerUid): array;
}
