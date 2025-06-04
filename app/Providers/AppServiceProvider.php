<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Notification\Sms\Contracts\SmsSenderInterface;
use App\Services\Notification\Sms\Providers\MeliPayamak\MeliPayamak;
use Illuminate\Pagination\Paginator;

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
        $this->app->bind(SmsSenderInterface::class, MeliPayamak::class);
        Paginator::useBootstrap(); // or useBootstrapFive() or useTailwind() depending on your setup

    }
}
