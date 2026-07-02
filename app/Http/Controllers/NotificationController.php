<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function match(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== \App\Enums\Role::SEEKER) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $careSeeker = $user->careSeeker;
        if (! $careSeeker) {
            return response()->json(['message' => 'CareSeeker profile not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $matchedGivers = $this->notificationService->generateMatches($careSeeker->uid);

            return response()->json($matchedGivers, Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! in_array($user->role, [Role::SEEKER, Role::GIVER])) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $notifications = $this->notificationService->getAllForUser($user);

        return response()->json($notifications, Response::HTTP_OK);
    }
}
