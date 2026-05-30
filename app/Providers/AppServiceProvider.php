<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            // Cache the settings model to prevent hitting the DB on every single request
            $settings = Cache::remember('app_settings', now()->addDay(), function () {
                return \App\Models\Setting::first();
            });

            if ($settings) {
                // Share settings globally with all views
                view()->share('settings', $settings);

                if ($settings->timezone) {
                    date_default_timezone_set($settings->timezone);
                    config(['app.timezone' => $settings->timezone]);
                }
            }
        } catch (\Exception $e) {
            // Ignore if table or cache is not ready
        }
    }
}

