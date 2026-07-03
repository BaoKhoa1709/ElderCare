<?php

namespace App\Services;

use App\Dto\CareSeekerDto;
use App\Enums\NotificationType;
use App\Enums\Role;
use App\Models\Booking;
use App\Models\CareSeeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CareSeekerServiceImp implements CareSeekerService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function createCareSeeker(array $data): CareSeekerDto
    {
        $userUid = $data['user_uid'];

        $existingCareSeeker = CareSeeker::where('user_uid', $userUid)->first();
        if ($existingCareSeeker) {
            throw new \InvalidArgumentException('User already has a care seeker profile');
        }

        $user = User::where('uid', $userUid)->first();
        if (! $user) {
            throw new \InvalidArgumentException('User not found');
        }

        $careSeeker = DB::transaction(function () use ($data) {
            $careSeeker = CareSeeker::create([
                'uid' => (string) Str::uuid(),
                'user_uid' => $data['user_uid'],
                'dob' => $data['dob'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'preferred_giver_gender' => $data['preferred_giver_gender'] ?? null,
            ]);

            if (! empty($data['care_needs'])) {
                foreach ($data['care_needs'] as $need) {
                    DB::table('care_seeker_care_needs')->insert([
                        'care_seeker_uid' => $careSeeker->uid,
                        'care_need' => $need,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (! empty($data['health_conditions'])) {
                foreach ($data['health_conditions'] as $condition) {
                    DB::table('care_seeker_health_conditions')->insert([
                        'care_seeker_uid' => $careSeeker->uid,
                        'health_condition' => $condition,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return $careSeeker;
        });

        try {
            $matches = $this->notificationService->findMatchesForCareSeeker($careSeeker->uid);

            if (! empty($matches)) {
                $message = $this->buildMatchFoundMessage($matches, $user);
                Notification::create([
                    'user_id' => $userUid,
                    'type' => NotificationType::MATCH_FOUND,
                    'message' => $message,
                    'is_read' => true,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate matches on CareSeeker creation: '.$e->getMessage());
        }

        $careSeekerWithRelations = CareSeeker::with(['careNeedRecords', 'healthConditionRecords'])
            ->where('uid', $careSeeker->uid)
            ->first();

        $careSeekerArray = $careSeekerWithRelations->toArray();
        $careSeekerArray['full_name'] = $user->full_name;
        $careSeekerArray['email'] = $user->email;

        return CareSeekerDto::fromArray($careSeekerArray);
    }

    private function buildMatchFoundMessage(array $matches, User $user): string
    {
        $careGiverNames = array_map(fn ($m) => $m['careGiver']->user->full_name ?? 'Unknown', $matches);
        $count = count($matches);

        if ($count === 1) {
            return "One care giver matched for {$user->full_name}: ".$careGiverNames[0];
        }

        return "{$count} care givers matched for {$user->full_name}: ".implode(', ', $careGiverNames);
    }

    public function getAll(): array
    {
        return CareSeeker::all()->map(fn ($cs) => CareSeekerDto::fromArray($cs->toArray()))->all();
    }

    public function getById(User $authUser, string $seekerUid): CareSeekerDto
    {
        $careSeeker = CareSeeker::with(['careNeedRecords', 'healthConditionRecords'])->where('uid', $seekerUid)->first();

        if (! $careSeeker) {
            throw new \InvalidArgumentException('CareSeeker not found');
        }

        $isAdmin = $authUser->role === Role::ADMIN;
        $isSeekerOwner = $authUser->careSeeker && $authUser->careSeeker->uid === $seekerUid;
        $hasBooking = Booking::where('care_seeker_uid', $seekerUid)
            ->where('care_giver_uid', $authUser->uid)
            ->exists();

        if (! $isAdmin && ! $isSeekerOwner && ! $hasBooking) {
            throw new \InvalidArgumentException('Unauthorized');
        }

        $careSeeker->load('user');
        $careSeekerArray = $careSeeker->toArray();
        $careSeekerArray['full_name'] = $careSeeker->user->full_name ?? null;
        $careSeekerArray['email'] = $careSeeker->user->email ?? null;

        return CareSeekerDto::fromArray($careSeekerArray);
    }

    public function deleteById(string $uid): bool
    {
        $careSeeker = CareSeeker::where('uid', $uid)->first();

        if (! $careSeeker) {
            return false;
        }

        return $careSeeker->delete();
    }
}
