<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckOrderDeliveryReminders;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            CheckOrderDeliveryReminders::class,
        ]);
        
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('orders:check-reminders')->hourly();
        });
    }
}
