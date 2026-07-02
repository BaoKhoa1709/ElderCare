<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'care_location' => 'required|string|in:CLINIC,AT_HOME',
            'from_date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'status' => 'nullable|string|in:PENDING,CONFIRMED,COMPLETED,CANCELED',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s|after:start_time',
            'care_seeker_uid' => 'required|string|exists:care_seekers,uid',
            'care_giver_uid' => 'required|string|exists:care_givers,uid',
            'note' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'payment' => 'required|string|in:ONLINE,ON_SITE',
        ]);

        $data['status'] = $data['status'] ?? 'PENDING';

        try {
            $bookingDto = $this->bookingService->create($data);

            return (new BookingResource($bookingDto))
                ->response()
                ->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getByUid(string $uid): JsonResponse
    {
        $bookingDto = $this->bookingService->getByUid($uid);

        if (! $bookingDto) {
            return response()->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        return (new BookingResource($bookingDto))
            ->response()
            ->setStatusCode(200);
    }

    public function getAll(): JsonResponse
    {
        $bookings = $this->bookingService->getAll();

        return BookingResource::collection($bookings)
            ->response()
            ->setStatusCode(200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'uid' => 'required|string|exists:bookings,uid',
            'status' => 'required|string|in:PENDING,CONFIRMED,COMPLETED,CANCELED',
        ]);

        try {
            $bookingDto = $this->bookingService->updateStatus($data['uid'], BookingStatus::from($data['status']));

            return (new BookingResource($bookingDto))
                ->response()
                ->setStatusCode(200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(string $uid): JsonResponse
    {
        $result = $this->bookingService->delete($uid);

        if (! $result) {
            return response()->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
