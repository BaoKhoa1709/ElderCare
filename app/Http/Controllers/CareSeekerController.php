<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\CareSeekerStoreRequest;
use App\Http\Resources\CareSeekerResource;
use App\Services\CareSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CareSeekerController extends Controller
{
    public function __construct(
        private CareSeekerService $careSeekerService
    ) {}

    public function store(CareSeekerStoreRequest $request): JsonResponse
    {
        $userUid = $request->user()->uid;
        $data = $request->validated();
        $data['user_uid'] = $userUid;

        try {
            $careSeekerDto = $this->careSeekerService->createCareSeeker($data);

            return (new CareSeekerResource($careSeekerDto))
                ->response()
                ->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll(): JsonResponse
    {
        $user = request()->user();

        if ($user->role !== Role::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $careSeekers = $this->careSeekerService->getAll();

        return CareSeekerResource::collection($careSeekers)
            ->response()
            ->setStatusCode(200);
    }

    public function getById(string $uid): JsonResponse
    {
        $user = request()->user();

        try {
            $careSeekerDto = $this->careSeekerService->getById($user, $uid);

            return (new CareSeekerResource($careSeekerDto))
                ->response()
                ->setStatusCode(200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteById(string $uid): JsonResponse
    {
        $user = request()->user();

        if ($user->role !== Role::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $deleted = $this->careSeekerService->deleteById($uid);

        if (! $deleted) {
            return response()->json(['message' => 'CareSeeker not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'CareSeeker deleted'], Response::HTTP_OK);
    }
}
