<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\UserRepository;
use App\Repository\ReviewRepositoryInterface;
use App\Repository\Eloquent\ReviewRepository;
use App\Repository\RentalRepositoryInterface;
use App\Repository\Eloquent\RentalRepository;
use App\Repository\EquipmentRepositoryInterface;
use App\Repository\Eloquent\EquipmentRepository;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(RentalRepositoryInterface::class, RentalRepository::class);
        $this->app->bind(EquipmentRepositoryInterface::class, EquipmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'https://')) { //CHATGPT
            URL::forceScheme('https');
        }

    }
}
