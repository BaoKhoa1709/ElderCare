<?php

namespace App\Services;

use App\Dto\ScheduleDto;
use App\Models\CaregiverSchedule;

interface ScheduleService
{
    public function createOrUpdateGiverSchedule(string $careGiverUid, ScheduleDto $schedule): CaregiverSchedule;
}