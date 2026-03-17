<?php

namespace App\Providers;

use App\Interface\BuyerInterface;
use App\Interface\ProductCategoryInterface;
use App\Interface\StoreBalanceHistoryInterface;
use App\Interface\StoreBalanceInterface;
use App\Interface\StoreRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Interface\WithdrawalInterface;
use App\Repositories\BuyerRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\StoreBalanceHistoryRepository;
use App\Repositories\StoreBalanceRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use App\Repositories\WithdrawalRepository;
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
        $this->app->bind(WithdrawalInterface::class, WithdrawalRepository::class);
        $this->app->bind(BuyerInterface::class, BuyerRepository::class);
        $this->app->bind(ProductCategoryInterface::class, ProductCategoryRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
