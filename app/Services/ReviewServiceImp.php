<?php

namespace App\Services;

use App\Dto\ReviewDto;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\Review;
use App\Models\User;

class ReviewServiceImp implements ReviewService
{
    public function create(User $user, array $data): ReviewDto
    {
        $booking = Booking::findOrFail($data['bookingId']);

        $careSeeker = CareSeeker::where('user_uid', $user->uid)->first();

        if (! $careSeeker || $booking->care_seeker_uid !== $careSeeker->uid) {
            throw new \InvalidArgumentException("You don't have permission to review this booking !");
        }

        if ($booking->status !== BookingStatus::COMPLETED) {
            throw new \InvalidArgumentException("This booking is not done for you to write a review !");
        }

        $review = Review::create([
            'booking_id' => $data['bookingId'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return ReviewDto::fromArray($review->toArray());
    }

    public function getByCareGiverUid(string $careGiverUid): array
    {
        $careGiver = CareGiver::findOrFail($careGiverUid);

        $bookings = $careGiver->bookings()->with('reviews')->get();

        $reviews = [];
        foreach ($bookings as $booking) {
            foreach ($booking->reviews as $review) {
                $reviews[] = ReviewDto::fromArray($review->toArray());
            }
        }

        if (empty($reviews)) {
            throw new \RuntimeException("Maybe this CareGiver has no reviews");
        }

        return $reviews;
    }

    public function update(User $user, int $reviewId, array $data): ReviewDto
    {
        $review = Review::findOrFail($reviewId);

        $careSeeker = CareSeeker::where('user_uid', $user->uid)->first();

        if (! $careSeeker || $review->booking->care_seeker_uid !== $careSeeker->uid) {
            throw new \InvalidArgumentException("You don't have permission to update this review.");
        }

        if (isset($data['rating'])) {
            $review->rating = $data['rating'];
        }
        if (isset($data['comment'])) {
            $review->comment = $data['comment'];
        }

        $review->save();

        return ReviewDto::fromArray($review->toArray());
    }

    public function delete(User $user, int $reviewId): string
    {
        $review = Review::findOrFail($reviewId);

        $careSeeker = CareSeeker::where('user_uid', $user->uid)->first();

        if (! $careSeeker || $review->booking->care_seeker_uid !== $careSeeker->uid) {
            throw new \InvalidArgumentException("You don't have permission to delete this review");
        }

        $review->delete();

        return "Review deleted successfully";
    }
}
