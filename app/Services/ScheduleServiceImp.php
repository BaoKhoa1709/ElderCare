<?php

namespace App\Services;

use App\Dto\ScheduleDto;
use App\Models\CareGiver;
use App\Models\CaregiverSchedule;

class ScheduleServiceImp implements ScheduleService
{
    public function createOrUpdateGiverSchedule(string $careGiverUid, ScheduleDto $schedule): CaregiverSchedule
    {
        $this->checkValidSchedule($schedule);

        $careGiver = CareGiver::where('uid', $careGiverUid)->first();

        if (!$careGiver) {
            throw new \InvalidArgumentException('CareGiver not found');
        }

        $scheduleModel = CaregiverSchedule::create([
            'care_giver_uid' => $careGiverUid,
            'day_of_weeks' => $schedule->days,
            'start_time' => $schedule->startTime,
            'end_time' => $schedule->endTime,
        ]);

        return $scheduleModel;
    }

    private function checkValidSchedule(ScheduleDto $schedule): void
    {
        if (empty($schedule->days)) {
            throw new \InvalidArgumentException('Schedule must have at least one day');
        }
    }
}