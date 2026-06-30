<?php

namespace App\Services;

use App\Dto\CareGiverDto;
use App\Models\CareGiver;
use App\Models\CareGiverSkill;
use App\Models\CaregiverCertification;
use App\Models\CaregiverSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareGiverServiceImp implements CareGiverService
{
    public function createCareGiver(array $data): CareGiverDto
    {
        $careGiver = DB::transaction(function () use ($data) {
            $careGiver = CareGiver::create([
                'uid' => (string) Str::uuid(),
                'user_uid' => $data['user_uid'],
                'dob' => $data['dob'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'year_experience' => $data['year_experience'] ?? null,
                'fee' => $data['fee'] ?? null,
                'bio' => $data['bio'] ?? null,
                'image_url' => $data['image_url'] ?? null,
            ]);

            if (!empty($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    CareGiverSkill::create([
                        'care_giver_uid' => $careGiver->uid,
                        'skill_name' => $skill,
                    ]);
                }
            }

            if (!empty($data['certifications'])) {
                foreach ($data['certifications'] as $cert) {
                    CaregiverCertification::create([
                        'care_giver_uid' => $careGiver->uid,
                        'certificate_name' => $cert['name'] ?? '',
                        'issuer' => $cert['issuer'] ?? null,
                        'issue_date' => $cert['issue_date'] ?? null,
                        'expiration_date' => $cert['expiration_date'] ?? null,
                    ]);
                }
            }

            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $schedule) {
                    CaregiverSchedule::create([
                        'care_giver_uid' => $careGiver->uid,
                        'day_of_weeks' => $schedule['days'] ?? [],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                    ]);
                }
            }

            return $careGiver;
        });

        return CareGiverDto::fromArray($careGiver->toArray());
    }

    public function getByUid(string $uid): ?CareGiverDto
    {
        $careGiver = CareGiver::with(['skills', 'certifications', 'schedules'])->where('uid', $uid)->first();

        if (!$careGiver) {
            return null;
        }

        return CareGiverDto::fromArray($careGiver->toArray());
    }

    public function getAll(): array
    {
        return CareGiver::all()->map(fn($cg) => CareGiverDto::fromArray($cg->toArray()))->all();
    }

    public function deleteByUid(string $uid): bool
    {
        $careGiver = CareGiver::where('uid', $uid)->first();

        if (!$careGiver) {
            return false;
        }

        return $careGiver->delete();
    }

    public function searchByName(string $name): array
    {
        $users = \App\Models\User::where('full_name', 'like', "%{$name}%")->get();

        return $users->flatMap(function ($user) {
            if ($user->careGiver) {
                return [CareGiverDto::fromArray($user->careGiver->toArray())];
            }

            return [];
        })->all();
    }
}
