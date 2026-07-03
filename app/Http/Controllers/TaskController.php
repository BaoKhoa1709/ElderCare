<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'taskName' => 'required|string|min:1|max:100',
            'type' => 'required|string|in:NEW_MESSAGE,MATCH_FOUND,BOOKING_PENDING,BOOKING_CONFIRMED,BOOKING_COMPLETED,BOOKING_CANCELED,REVIEW_RECEIVED,PAYMENT_RECEIVED,TRAINING_AVAILABLE',
            'bookingId' => 'required|integer|exists:bookings,id',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $taskDto = $this->taskService->create($user, $data);

            return (new TaskResource($taskDto))->response()->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getById(int $taskId): JsonResponse
    {
        $taskDto = $this->taskService->getById($taskId);

        if (! $taskDto) {
            return response()->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return (new TaskResource($taskDto))->response()->setStatusCode(200);
    }

    public function getAll(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $tasks = $this->taskService->getAll($user);

        return TaskResource::collection($tasks)->response()->setStatusCode(200);
    }

    public function getAllByBooking(Request $request, int $bookingId): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $tasks = $this->taskService->getAllByBooking($bookingId, $user);

        return TaskResource::collection($tasks)->response()->setStatusCode(200);
    }

    public function update(Request $request, int $taskId): JsonResponse
    {
        $data = $request->validate([
            'taskName' => 'sometimes|string|min:1|max:100',
            'type' => 'sometimes|string|in:NEW_MESSAGE,MATCH_FOUND,BOOKING_PENDING,BOOKING_CONFIRMED,BOOKING_COMPLETED,BOOKING_CANCELED,REVIEW_RECEIVED,PAYMENT_RECEIVED,TRAINING_AVAILABLE',
            'bookingId' => 'sometimes|integer|exists:bookings,id',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $taskDto = $this->taskService->update($user, $taskId, $data);

            return (new TaskResource($taskDto))->response()->setStatusCode(200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request, int $taskId): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $message = $this->taskService->delete($user, $taskId);

            return response()->json(['message' => $message], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
