<?php

namespace App\Services;

use App\Dto\CertificationDto;
use App\Models\CareGiver;
use App\Models\CaregiverCertification;

class CertificationServiceImp implements CertificationService
{
    public function createOrUpdateGiverCert(string $giverUid, array $certificationDto): array
    {
        foreach ($certificationDto as $cert) {
            $this->checkValidDate($cert['issue_date'] ?? null, $cert['expiration_date'] ?? null);
        }

        $careGiver = CareGiver::where('uid', $giverUid)->first();

        if (!$careGiver) {
            throw new \InvalidArgumentException('CareGiver not found');
        }

        $certifications = collect($certificationDto)->map(fn($cert) => CaregiverCertification::create([
            'care_giver_uid' => $giverUid,
            'certificate_name' => $cert['certificate_name'],
            'issuer' => $cert['issuer'] ?? null,
            'issue_date' => $cert['issue_date'] ?? null,
            'expiration_date' => $cert['expiration_date'] ?? null,
        ]));

        return $certificationDto;
    }

    private function checkValidDate(?string $issueDate, ?string $expirationDate): void
    {
        if ($issueDate === null || $expirationDate === null) {
            throw new \InvalidArgumentException('issueDate and expirationDate must not be null');
        }

        $issue = \Carbon\Carbon::parse($issueDate);
        $expiration = \Carbon\Carbon::parse($expirationDate);

        if ($issue->isAfter($expiration)) {
            throw new \InvalidArgumentException('issueDate must be before expirationDate');
        }
    }
}