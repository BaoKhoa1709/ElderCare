<?php

namespace App\Services;

use App\Dto\CertificationDto;

interface CertificationService
{
    public function createOrUpdateGiverCert(string $giverUid, array $certificationDto): array;
}