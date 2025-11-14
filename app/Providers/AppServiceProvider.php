<?php

namespace App\Providers;
require_once app_path('Helpers/telegram.php');

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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            // Ensure helper functions are loaded (telegram helper)
            $helper = app_path('Helpers/telegram.php');
            if (file_exists($helper)) {
                require_once $helper;
            }
    }
}
