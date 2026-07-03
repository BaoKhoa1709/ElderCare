<?php

namespace App\Services;

use App\Dto\CareNeedOrSkillsDto;
use App\Models\CareGiver;
use App\Models\CareGiverSkill;
use App\Models\CareSeeker;
use App\Models\CareSeekerCareNeed;

class CareNeedOrSkillsServiceImp implements CareNeedOrSkillsService
{
    public function updateCareNeedOrSkills(string $seekerOrGiverUid, CareNeedOrSkillsDto $careNeedDto): CareNeedOrSkillsDto
    {
        $careGiver = CareGiver::where('uid', $seekerOrGiverUid)->first();

        if ($careGiver) {
            CareGiverSkill::where('care_giver_uid', $seekerOrGiverUid)->delete();

            $skills = collect($careNeedDto->careNeedOrSkills)->map(fn($skill) => CareGiverSkill::create([
                'care_giver_uid' => $seekerOrGiverUid,
                'skill_name' => $skill,
            ]));

            return $careNeedDto;
        }

        $careSeeker = CareSeeker::where('uid', $seekerOrGiverUid)->first();

        if ($careSeeker) {
            CareSeekerCareNeed::where('care_seeker_uid', $seekerOrGiverUid)->delete();

            foreach ($careNeedDto->careNeedOrSkills as $careNeed) {
                CareSeekerCareNeed::create([
                    'care_seeker_uid' => $seekerOrGiverUid,
                    'care_need' => $careNeed,
                ]);
            }

            return $careNeedDto;
        }

        throw new \RuntimeException('Not found CareGiver and CareSeeker');
    }
}