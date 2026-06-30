<?php

namespace App\Services;

use App\Dto\LoginUserRequest;
use App\Dto\RegisterUserRequest;
use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserServiceImp implements UserService
{
    public function register(RegisterUserRequest $request): User
    {
        return User::create([
            'uid' => (string) \Illuminate\Support\Str::uuid(),
            'full_name' => $request->fullName(),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'role' => Role::USER->value,
        ]);
    }

    public function authenticateUser(LoginUserRequest $request): ?User
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return null;
        }

        return $user;
    }
}
