<?php

namespace App\Dto;

class CertificationDto
{
    public function __construct(
        public string $certificateName,
        public ?string $issuer = null,
        public ?string $issueDate = null,
        public ?string $expirationDate = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            certificateName: $data['certificate_name'],
            issuer: $data['issuer'] ?? null,
            issueDate: $data['issue_date'] ?? null,
            expirationDate: $data['expiration_date'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'certificate_name' => $this->certificateName,
            'issuer' => $this->issuer,
            'issue_date' => $this->issueDate,
            'expiration_date' => $this->expirationDate,
        ];
    }
}