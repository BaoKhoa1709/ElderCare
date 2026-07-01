<?php

namespace App\Services;

interface AiRecommendationService
{
    public function getRecommendations(string $careSeekerUid): array;
    public function generateRecommendations(string $careSeekerUid): array;
}