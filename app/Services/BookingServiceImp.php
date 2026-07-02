<?php

namespace App\Services;

use App\Dto\BookingDto;
use App\Enums\BookingStatus;
use App\Enums\NotificationType;
use App\Enums\Payment;
use App\Enums\Role;
use App\Models\Booking;
use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingServiceImp implements BookingService
{
    public function __construct(private MomoService $momoService) {}

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

    public function decide(int $bookingId, string $type, ?string $meetingLink, User $user): string
    {
        $booking = Booking::where('id', $bookingId)->first();

        if (! $booking) {
            throw new \InvalidArgumentException('Booking not found');
        }

        $careGiverUid = $booking->care_giver_uid;
        $careSeekerUid = $booking->care_seeker_uid;

        if ($user->uid !== $careGiverUid && $user->uid !== $careSeekerUid) {
            throw new \InvalidArgumentException('Unauthorized');
        }

        if ($type === BookingStatus::CONFIRMED->value) {
            if ($user->role !== Role::GIVER) {
                throw new \InvalidArgumentException('Only caregiver can confirm');
            }

            if ($meetingLink) {
                $booking->meeting_link = $meetingLink;
            }

            $booking->status = BookingStatus::CONFIRMED;
            $booking->save();

            $this->sendBookingConfirmed($booking);

            return 'Booking confirmed';
        }

        if ($type === BookingStatus::CANCELED->value) {
            $this->sendBookingCanceled($booking, $careGiverUid, $careSeekerUid);

            $booking->delete();

            return 'Booking canceled';
        }

        $booking->status = BookingStatus::from($type);
        $booking->save();

        return 'Booking status updated';
    }

    private function sendBookingConfirmed(Booking $booking): void
    {
        $seeker = CareSeeker::where('uid', $booking->care_seeker_uid)->with('user')->first();

        if ($seeker && $seeker->user) {
            Notification::create([
                'user_uid' => $seeker->user->uid,
                'care_seeker_uid' => $booking->care_seeker_uid,
                'care_giver_uid' => $booking->care_giver_uid,
                'type' => NotificationType::BOOKING_CONFIRMED->value,
                'message' => 'Lịch hẹn của bạn đã được xác nhận.',
                'is_read' => false,
            ]);
        }

        $giver = CareGiver::where('uid', $booking->care_giver_uid)->with('user')->first();

        if ($seeker && $seeker->user && $seeker->user->email) {
            $body = 'Lịch hẹn của bạn đã được xác nhận.';

            if ($booking->payment === Payment::ONLINE && $giver) {
                $momoLink = $this->momoService->createSandboxMomoLink(
                    (string) $booking->id,
                    (string) ($giver->fee ?? '0')
                );

                $body .= ' Vui lòng thanh toán tại đây: ' . $momoLink;
            }

            Mail::raw($body, function ($message) use ($seeker) {
                $message->to($seeker->user->email)->subject('Booking Confirmed');
            });
        }
    }

    private function sendBookingCanceled(Booking $booking, string $giverUid, string $seekerUid): void
    {
        foreach ([$giverUid, $seekerUid] as $uid) {
            Notification::create([
                'user_uid' => $uid,
                'care_seeker_uid' => $seekerUid,
                'care_giver_uid' => $giverUid,
                'type' => NotificationType::BOOKING_CANCELED->value,
                'message' => 'Lịch hẹn của bạn đã bị hủy.',
                'is_read' => false,
            ]);
        }
    }
}
