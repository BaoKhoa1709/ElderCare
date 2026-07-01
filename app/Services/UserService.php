<?php

namespace App\Services;

use App\Dto\LoginUserRequest;
use App\Dto\RegisterUserRequest;
use App\Models\User;

interface UserService
{
    public function register(RegisterUserRequest $request): User;
    public function authenticateUser(LoginUserRequest $request): ?User;
    public function sendResetPasswordEmail(string $email): string;
}
