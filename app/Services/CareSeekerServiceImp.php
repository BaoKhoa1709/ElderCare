<?php

namespace App\Services;

use App\Dto\CareSeekerDto;
use App\Models\CareSeeker;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareSeekerServiceImp implements CareSeekerService
{
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

        $careSeekerWithRelations = CareSeeker::with(['careNeedRecords', 'healthConditionRecords'])
            ->where('uid', $careSeeker->uid)
            ->first();

        $careSeekerArray = $careSeekerWithRelations->toArray();
        $careSeekerArray['full_name'] = $user->full_name;
        $careSeekerArray['email'] = $user->email;

        return CareSeekerDto::fromArray($careSeekerArray);
    }

    public function getAll(): array
    {
        return CareSeeker::all()->map(fn ($cs) => CareSeekerDto::fromArray($cs->toArray()))->all();
    }

    public function getById(string $uid): ?CareSeekerDto
    {
        $careSeeker = CareSeeker::with(['careNeedRecords', 'healthConditionRecords'])->where('uid', $uid)->first();

        if (! $careSeeker) {
            return null;
        }

        return CareSeekerDto::fromArray($careSeeker->toArray());
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
