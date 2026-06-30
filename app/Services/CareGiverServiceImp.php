<?php

namespace App\Services;

use App\Dto\CareGiverDto;
use App\Models\CareGiver;
use App\Models\CareGiverSkill;
use App\Models\CaregiverCertification;
use App\Models\CaregiverSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareGiverServiceImp implements CareGiverService
{
    public function createCareGiver(array $data): CareGiverDto
    {
        $userUid = $data['user_uid'];
        $phoneNumber = $data['phone_number'];

        $existingCareGiver = CareGiver::where('user_uid', $userUid)->first();
        if ($existingCareGiver) {
            throw new \InvalidArgumentException('User already has a caregiver profile');
        }

        $phoneExists = CareGiver::where('phone_number', $phoneNumber)->first();
        if ($phoneExists) {
            throw new \InvalidArgumentException('Phone number already registered');
        }

        $user = User::where('uid', $userUid)->first();
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $careGiver = DB::transaction(function () use ($data, $user) {
            $careGiver = CareGiver::create([
                'uid' => (string) Str::uuid(),
                'user_uid' => $data['user_uid'],
                'dob' => $data['dob'],
                'phone_number' => $data['phone_number'],
                'year_experience' => $data['year_experience'],
                'fee' => $data['fee'],
                'bio' => $data['bio'],
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
                        'certificate_name' => $cert['name'],
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
                        'day_of_weeks' => $schedule['days'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                    ]);
                }
            }

            return $careGiver;
        });

        $careGiverWithRelations = CareGiver::with(['skills', 'certifications', 'schedules'])
            ->where('uid', $careGiver->uid)
            ->first();

        $careGiverArray = $careGiverWithRelations->toArray();
        $careGiverArray['full_name'] = $user->full_name;
        $careGiverArray['email'] = $user->email;

        return CareGiverDto::fromArray($careGiverArray);
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
        $users = User::where('full_name', 'like', "%{$name}%")->get();

        return $users->flatMap(function ($user) {
            if ($user->careGiver) {
                return [CareGiverDto::fromArray($user->careGiver->toArray())];
            }

            return [];
        })->all();
    }

    public function uploadFile(string $tempPath): string
    {
        return Storage::disk('public')->putFile('caregivers', $tempPath);
    }

    public function linkImageToGiver(string $giverUid, string $filePath): string
    {
        $careGiver = CareGiver::where('uid', $giverUid)->first();

        if (!$careGiver) {
            throw new \InvalidArgumentException('CareGiver not found');
        }

        $storedPath = $this->uploadFile($filePath);
        $careGiver->image_url = $storedPath;
        $careGiver->save();

        return 'Image linked successfully';
    }
}