<?php

namespace App\Providers;

use App\Interface\StoreBalanceHistoryInterface;
use App\Interface\StoreBalanceInterface;
use App\Interface\StoreRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Repositories\StoreBalanceHistoryRepository;
use App\Repositories\StoreBalanceRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(StoreBalanceInterface::class, StoreBalanceRepository::class);
        $this->app->bind(StoreBalanceHistoryInterface::class, StoreBalanceHistoryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
