<?php

namespace App\Dto;

class RegisterUserRequest
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public string $gender
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            email: $data['email'],
            password: $data['password'],
            gender: $data['gender']
        );
    }

    public function fullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
