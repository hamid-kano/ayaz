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
        // تعيين التوقيت الافتراضي لـ Carbon
        \Carbon\Carbon::setDefaultTimezone(config('app.timezone'));
        
        // تعيين التوقيت الافتراضي لـ PHP
        date_default_timezone_set(config('app.timezone'));
        
        $this->commands([
            CheckOrderDeliveryReminders::class,
        ]);
        
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('orders:check-reminders')->hourly();
        });
    }
}
