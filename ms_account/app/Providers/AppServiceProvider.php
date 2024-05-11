<?php

namespace App\Providers;

use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Repositories\Eloquent\AccountEloquentRepository;
use App\Repositories\Eloquent\TransactionEloquentRepository;
use App\Repositories\Eloquent\UserEloquentRepository;
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
        $this->app->bind(UserEntityRepositoryInterface::class, UserEloquentRepository::class);
        $this->app->bind(AccountEntityRepositoryInterface::class, AccountEloquentRepository::class);
        $this->app->bind(RabbitInterface::class, AMQPService::class);
        $this->app->bind(TransactionEntityRepositoryInterface::class, TransactionEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
