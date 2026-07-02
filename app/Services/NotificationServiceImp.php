<?php

namespace App\Services;

use App\Dto\NotificationsDto;
use App\Enums\NotificationType;
use App\Enums\Role;
use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationServiceImp implements NotificationService
{
    public function getNotifications(string $careSeekerUid): array
    {
        return Notification::where('care_seeker_uid', $careSeekerUid)
            ->whereNotNull('match_point')
            ->with(['careGiver.user'])
            ->get()
            ->map(fn ($n) => [
                'careGiverUid' => $n->care_giver_uid,
                'matchPoint' => $n->match_point,
                'careGiverName' => $n->careGiver->user->full_name ?? null,
            ])
            ->all();
    }

    public function findMatchesForCareSeeker(string $careSeekerUid): array
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

        $matches = [];

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

            Log::info('Match points found for care giver '.$careGiver->uid.': '.$score);

            if ($score >= 40) {
                $matches[] = [
                    'careGiver' => $careGiver,
                    'score' => $score,
                ];
            }
        }

        return $matches;
    }

    public function generateMatches(string $careSeekerUid): array
    {
        $matches = $this->findMatchesForCareSeeker($careSeekerUid);
        $matchCount = count($matches);

        foreach ($matches as $match) {
            Notification::create([
                'care_seeker_uid' => $careSeekerUid,
                'care_giver_uid' => $match['careGiver']->uid,
                'match_point' => $match['score'],
                'type' => NotificationType::MATCH_FOUND->value,
                'message' => "Tìm thấy {$matchCount} CareGiver phù hợp",
                'is_read' => false,
            ]);
        }

        return $this->getNotifications($careSeekerUid);
    }

    public function getAllForUser(User $user): array
    {
        $notifications = Notification::where('user_uid', $user->uid)
            ->orderByDesc('created_at')
            ->with(['careGiver.user'])
            ->get();

        return $notifications->map(fn ($n) => $this->buildNotificationByType($n, $user)->toArray())->all();
    }

    private function buildNotificationByType(Notification $n, User $user): NotificationsDto
    {
        $createdAt = $n->created_at?->toIso8601String();

        return match ($n->type->value) {
            NotificationType::MATCH_FOUND->value => $this->buildMatchFound($n, $user),
            NotificationType::BOOKING_CONFIRMED->value => NotificationsDto::fromType(
                NotificationType::BOOKING_CONFIRMED->value,
                'Lịch hẹn của bạn đã được xác nhận.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            NotificationType::BOOKING_CANCELED->value => NotificationsDto::fromType(
                NotificationType::BOOKING_CANCELED->value,
                'Lịch hẹn của bạn đã bị hủy.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            NotificationType::NEW_MESSAGE->value => NotificationsDto::fromType(
                NotificationType::NEW_MESSAGE->value,
                'Bạn có tin nhắn mới.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            NotificationType::REVIEW_RECEIVED->value => NotificationsDto::fromType(
                NotificationType::REVIEW_RECEIVED->value,
                'Bạn vừa nhận được đánh giá từ người dùng.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            NotificationType::PAYMENT_RECEIVED->value => NotificationsDto::fromType(
                NotificationType::PAYMENT_RECEIVED->value,
                'Bạn đã nhận được một khoản thanh toán.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            NotificationType::TRAINING_AVAILABLE->value => NotificationsDto::fromType(
                NotificationType::TRAINING_AVAILABLE->value,
                'Một khóa đào tạo mới đang sẵn có cho bạn.',
                $n->id,
                $n->is_read,
                $createdAt
            ),
            default => NotificationsDto::fromType(
                $n->type?->value ?? 'UNKNOWN',
                'Thông báo không xác định',
                $n->id,
                $n->is_read,
                $createdAt
            ),
        };
    }

    private function buildMatchFound(Notification $n, User $user): NotificationsDto
    {
        $createdAt = $n->created_at?->toIso8601String();

        if ($user->role === Role::SEEKER && $n->care_seeker_uid === $user->uid) {
            $careGiver = CareGiver::where('uid', $n->care_giver_uid)->with('user')->first();

            if ($careGiver) {
                return NotificationsDto::fromMatch(
                    $careGiver,
                    $n->id,
                    $n->is_read,
                    $createdAt
                );
            }
        }

        return NotificationsDto::fromType(
            NotificationType::MATCH_FOUND->value,
            'Tìm thấy CareGiver phù hợp cho bạn',
            $n->id,
            $n->is_read,
            $createdAt
        );
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

        Log::info('AI matching prompt: '.$prompt);

        $response = $this->getAiMatchingResult($prompt);

        Log::info('AI raw response: '.$response);

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
            Log::error('AI matching error: '.$e->getMessage());

            return null;
        }
    }
}
