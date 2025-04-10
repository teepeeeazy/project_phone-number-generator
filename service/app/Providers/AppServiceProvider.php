<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use libphonenumber\PhoneNumberUtil;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind PhoneNumberUtil to the service container
        $this->app->singleton(PhoneNumberUtil::class, function () {
            return PhoneNumberUtil::getInstance();
        });
    }
}
