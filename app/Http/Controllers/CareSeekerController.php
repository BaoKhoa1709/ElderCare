<?php

namespace App\Http\Controllers;

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
}
