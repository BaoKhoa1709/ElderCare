<?php

namespace App\Services;

use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class NotificationServiceImp implements NotificationService
{
    public function getNotifications(string $careSeekerUid): array
    {
        return Notification::where('care_seeker_uid', $careSeekerUid)
            ->with(['careGiver.user'])
            ->get()
            ->map(fn ($n) => [
                'careGiverUid' => $n->care_giver_uid,
                'matchPoint' => $n->match_point,
                'careGiverName' => $n->careGiver->user->full_name ?? null,
            ])
            ->all();
    }

    public function generateMatches(string $careSeekerUid): array
    {
        $careSeeker = CareSeeker::with(['careNeedRecords', 'healthConditionRecords', 'user'])->where('uid', $careSeekerUid)->first();

        if (! $careSeeker) {
            throw new \InvalidArgumentException('CareSeeker not found');
        }

        $careSeekerCareNeeds = $careSeeker->careNeedRecords->pluck('care_need')->toArray();
        $careSeekerHealthConditions = $careSeeker->healthConditionRecords->pluck('health_condition')->toArray();
        $preferredGiverGender = $careSeeker->preferred_giver_gender;
        $careSeekerAddress = $careSeeker->user->address;

        $careGivers = CareGiver::with(['skills', 'certifications', 'user'])->get();

        $notifications = [];

        foreach ($careGivers as $careGiver) {
            $score = $this->checkAllCriteria(
                $careSeekerCareNeeds,
                $careSeekerHealthConditions,
                $preferredGiverGender?->value,
                $careSeekerAddress,
                $careGiver->skills->pluck('skill_name')->toArray(),
                $careGiver->certifications->pluck('certificate_name')->toArray(),
                $careGiver->user->gender?->value,
                $careGiver->user->address
            );

            if ($score >= 40) {
                $notifications[] = [
                    'care_seeker_uid' => $careSeekerUid,
                    'care_giver_uid' => $careGiver->uid,
                    'match_point' => $score,
                ];
            }
        }

        foreach ($notifications as $notif) {
            Notification::create($notif);
        }

        return $this->getNotifications($careSeekerUid);
    }

    private function checkAllCriteria(
        array $seekerCareNeeds,
        array $seekerHealthConditions,
        ?string $seekerGender,
        ?string $seekerAddress,
        array $giverSkills,
        array $giverCerts,
        ?string $giverGender,
        ?string $giverAddress
    ): int {
        $prompt = "Bạn là hệ thống matching. Hãy so sánh CareSeeker và CareGiver theo 4 tiêu chí: skill, health, gender, address.\n"
            ."Chỉ trả về JSON thuần túy theo format sau, không thêm chữ nào khác:\n"
            ."{\n"
            ."  \"skillMatch\": true/false,\n"
            ."  \"healthMatch\": true/false,\n"
            ."  \"genderMatch\": true/false,\n"
            ."  \"addressMatch\": true/false\n"
            ."}\n\n"
            .'CareSeeker cần: '.json_encode($seekerCareNeeds)
            .', tình trạng sức khỏe: '.json_encode($seekerHealthConditions)
            .', muốn CareGiver giới tính: '.($seekerGender ?? 'không yêu cầu')
            .', sống tại: '.($seekerAddress ?? 'không rõ').".\n\n"
            .'CareGiver có kỹ năng: '.json_encode($giverSkills)
            .', chứng chỉ: '.json_encode($giverCerts)
            .', giới tính: '.($giverGender ?? 'không rõ')
            .', sống tại: '.($giverAddress ?? 'không rõ').'.';

        $response = $this->getAiMatchingResult($prompt);

        if (! $response) {
            return 0;
        }

        $points = 0;
        try {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($data['skillMatch'] ?? false) {
                    $points += 20;
                }
                if ($data['healthMatch'] ?? false) {
                    $points += 20;
                }
                if ($data['genderMatch'] ?? false) {
                    $points += 20;
                }
                if ($data['addressMatch'] ?? false) {
                    $points += 20;
                }
            }
        } catch (\Exception $e) {
        }

        return $points;
    }

    private function getAiMatchingResult(string $prompt): ?string
    {
        $apiKey = Config::get('services.openai.key');
        $apiUrl = Config::get('services.openai.url');
        $model = Config::get('services.openai.model');

        if (! $apiKey || ! $apiUrl) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->contentType('application/json')
                ->post($apiUrl, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            $body = $response->json();

            return $body['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
