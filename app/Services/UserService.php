<?php

namespace App\Services;

use App\Dto\RegisterUserRequest;
use App\Models\User;

interface UserService
{
    public function register(RegisterUserRequest $request): User;
}
