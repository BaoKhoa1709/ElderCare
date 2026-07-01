<?php

namespace App\Providers;

use App\Services\CareNeedOrSkillsService;
use App\Services\CareNeedOrSkillsServiceImp;
use App\Services\CareGiverService;
use App\Services\CareGiverServiceImp;
use App\Services\CareSeekerService;
use App\Services\CareSeekerServiceImp;
use App\Services\UserService;
use App\Services\UserServiceImp;
use App\Services\CertificationService;
use App\Services\CertificationServiceImp;
use App\Services\ScheduleService;
use App\Services\ScheduleServiceImp;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserService::class, UserServiceImp::class);
        $this->app->bind(CareGiverService::class, CareGiverServiceImp::class);
        $this->app->bind(CareSeekerService::class, CareSeekerServiceImp::class);
        $this->app->bind(CareNeedOrSkillsService::class, CareNeedOrSkillsServiceImp::class);
        $this->app->bind(CertificationService::class, CertificationServiceImp::class);
        $this->app->bind(ScheduleService::class, ScheduleServiceImp::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
