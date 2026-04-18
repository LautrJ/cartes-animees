<?php

namespace App\Providers;

use App\Models\Child;
use App\Models\Setting;
use App\Observers\ChildObserver;
use App\Observers\SettingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Child::observe(ChildObserver::class);
        Setting::observe(SettingObserver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
