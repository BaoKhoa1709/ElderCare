<?php

namespace App\Http\Controllers\Auth;

use App\Dto\RegisterUserRequest as RegisterUserRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class RegisteredUserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function register(RegisterUserRequest $request)
    {
        $dto = RegisterUserRequestDto::fromArray($request->validated());
        $user = $this->userService->register($dto);

        $token = $user->createToken('registration-token');

        return (new UserResource($user))
            ->additional([
                'token' => $token->plainTextToken,
            ])
            ->response()
            ->setStatusCode(201);
    }
}
