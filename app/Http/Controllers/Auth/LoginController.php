<?php

namespace App\Http\Controllers\Auth;

use App\Dto\LoginUserRequest as LoginUserRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function login(LoginUserRequest $request)
    {
        $dto = LoginUserRequestDto::fromArray($request->validated());
        $user = $this->userService->authenticateUser($dto);

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('login-token');

        return (new UserResource($user))
            ->additional([
                'token' => $token->plainTextToken,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $message = $this->userService->sendResetPasswordEmail($request->email);
        return response()->json(['message' => $message], Response::HTTP_OK);
    }
}