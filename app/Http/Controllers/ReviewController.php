<?php

namespace App\Http\Controllers;

use App\Dto\ReviewDto;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bookingId' => 'required|integer|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $reviewDto = $this->reviewService->create($user, $data);
            return (new ReviewResource($reviewDto))->response()->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getByCareGiverUid(string $careGiverUid): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getByCareGiverUid($careGiverUid);
            return ReviewResource::collection($reviews)->response()->setStatusCode(200);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, int $reviewId): JsonResponse
    {
        $data = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $reviewDto = $this->reviewService->update($user, $reviewId, $data);
            return (new ReviewResource($reviewDto))->response()->setStatusCode(200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request, int $reviewId): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $message = $this->reviewService->delete($user, $reviewId);
            return response()->json(['message' => $message], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
