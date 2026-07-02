<?php

namespace App\Services;

use App\Dto\BookingDto;
use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingServiceImp implements BookingService
{
    public function create(array $data): BookingDto
    {
        $booking = DB::transaction(function () use ($data) {
            return Booking::create([
                'care_location' => $data['care_location'],
                'from_date' => $data['from_date'],
                'duration' => $data['duration'],
                'status' => $data['status'] ?? BookingStatus::PENDING->value,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'care_seeker_uid' => $data['care_seeker_uid'],
                'care_giver_uid' => $data['care_giver_uid'],
                'note' => $data['note'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
                'payment' => $data['payment'],
            ]);
        });

        return BookingDto::fromArray($booking->toArray());
    }

    public function getById(int $id): ?BookingDto
    {
        $booking = Booking::with(['careSeeker', 'careGiver'])->where('id', $id)->first();

        if (! $booking) {
            return null;
        }

        return BookingDto::fromArray($booking->toArray());
    }

    public function getAll(): array
    {
        return Booking::all()->map(fn ($b) => BookingDto::fromArray($b->toArray()))->all();
    }

    public function updateStatus(int $id, BookingStatus $status): BookingDto
    {
        $booking = Booking::where('id', $id)->first();

        if (! $booking) {
            throw new \InvalidArgumentException('Booking not found');
        }

        $booking->status = $status;
        $booking->save();

        return BookingDto::fromArray($booking->toArray());
    }

    public function delete(int $id): bool
    {
        $booking = Booking::where('id', $id)->first();

        if (! $booking) {
            return false;
        }

        return $booking->delete();
    }
}
