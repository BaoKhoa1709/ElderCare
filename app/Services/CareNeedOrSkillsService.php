<?php

namespace App\Services;

use App\Dto\CareNeedOrSkillsDto;

interface CareNeedOrSkillsService
{
    public function updateCareNeedOrSkills(string $seekerOrGiverUid, CareNeedOrSkillsDto $careNeedDto): CareNeedOrSkillsDto;
}