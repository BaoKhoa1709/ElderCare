<?php

namespace App\Providers;

use App\Services\BookingService;
use App\Services\BookingServiceImp;
use App\Services\CareGiverService;
use App\Services\CareGiverServiceImp;
use App\Services\CareNeedOrSkillsService;
use App\Services\CareNeedOrSkillsServiceImp;
use App\Services\CareSeekerService;
use App\Services\CareSeekerServiceImp;
use App\Services\CertificationService;
use App\Services\CertificationServiceImp;
use App\Services\NotificationService;
use App\Services\NotificationServiceImp;
use App\Services\ScheduleService;
use App\Services\ScheduleServiceImp;
use App\Services\UserService;
use App\Services\UserServiceImp;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookingService::class, BookingServiceImp::class);
        $this->app->bind(UserService::class, UserServiceImp::class);
        $this->app->bind(CareGiverService::class, CareGiverServiceImp::class);
        $this->app->bind(CareSeekerService::class, CareSeekerServiceImp::class);
        $this->app->bind(CareNeedOrSkillsService::class, CareNeedOrSkillsServiceImp::class);
        $this->app->bind(CertificationService::class, CertificationServiceImp::class);
        $this->app->bind(ScheduleService::class, ScheduleServiceImp::class);
        $this->app->bind(NotificationService::class, NotificationServiceImp::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
