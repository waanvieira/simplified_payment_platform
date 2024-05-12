<?php

namespace App\Providers;

use App\Gateway\BaseRequestClientService;
use App\Gateway\HttpInterface;
use App\Services\RabbitMQ\AMQPService;
use App\Services\RabbitMQ\RabbitInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HttpInterface::class, BaseRequestClientService::class);
        $this->app->bind(RabbitInterface::class, AMQPService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
