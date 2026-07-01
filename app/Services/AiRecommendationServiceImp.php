<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\CareGiver;
use App\Models\CareSeeker;
use App\Models\CareGiverSkill;
use App\Models\CaregiverCertification;
use App\Models\CareSeekerHealthCondition;
use App\Enums\CareNeed;
use App\Enums\HealthCondition;

class AiRecommendationServiceImp implements AiRecommendationService
{
    public function getRecommendations(string $careSeekerUid): array
    {
        return AiRecommendation::where('care_seeker_uid', $careSeekerUid)
            ->with(['careGiver.user'])
            ->get()
            ->map(fn($r) => [
                'careGiverUid' => $r->care_giver_uid,
                'matchPoint' => $r->match_point,
                'careGiverName' => $r->careGiver->user->full_name ?? null,
            ])
            ->all();
    }

    public function generateRecommendations(string $careSeekerUid): array
    {
        $careSeeker = CareSeeker::with(['careNeedRecords', 'healthConditionRecords', 'user'])->where('uid', $careSeekerUid)->first();

        if (!$careSeeker) {
            throw new \InvalidArgumentException('CareSeeker not found');
        }

        $careSeekerCareNeeds = $careSeeker->careNeedRecords->pluck('care_need')->toArray();
        $careSeekerHealthConditions = $careSeeker->healthConditionRecords->pluck('health_condition')->toArray();
        $preferredGiverGender = $careSeeker->preferred_giver_gender;
        $careSeekerAddress = $careSeeker->user->address;

        $careGivers = CareGiver::with(['skills', 'certifications', 'user'])->get();

        $recommendations = [];

        foreach ($careGivers as $careGiver) {
            $score = 0;

            $giverSkills = $careGiver->skills->pluck('skill_name')->toArray();
            $matchedSkills = array_intersect($careSeekerCareNeeds, $giverSkills);
            if (count($matchedSkills) > 0) {
                $score += 20;
            }

            $giverCertifications = $careGiver->certifications->pluck('certificate_name')->toArray();
            $matchedCerts = array_intersect($careSeekerHealthConditions, $giverCertifications);
            if (count($matchedCerts) > 0) {
                $score += 20;
            }

            $giverGender = $careGiver->user->gender->value ?? null;
            if ($preferredGiverGender && $giverGender === $preferredGiverGender) {
                $score += 20;
            }

            if ($careSeekerAddress && $careGiver->user->address) {
                $score += 20;
            }

            if ($score >= 60) {
                $recommendations[] = [
                    'care_seeker_uid' => $careSeekerUid,
                    'care_giver_uid' => $careGiver->uid,
                    'match_point' => $score,
                ];
            }
        }

        foreach ($recommendations as $rec) {
            AiRecommendation::create($rec);
        }

        return $this->getRecommendations($careSeekerUid);
    }
}