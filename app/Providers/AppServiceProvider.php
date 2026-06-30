<?php

namespace App\Providers;

use App\Services\CareGiverService;
use App\Services\CareGiverServiceImp;
use App\Services\CareSeekerService;
use App\Services\CareSeekerServiceImp;
use App\Services\UserService;
use App\Services\UserServiceImp;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserService::class, UserServiceImp::class);
        $this->app->bind(CareGiverService::class, CareGiverServiceImp::class);
        $this->app->bind(CareSeekerService::class, CareSeekerServiceImp::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
