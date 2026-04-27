<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\EquipmentRepositoryInterface;
use App\Repository\Eloquent\EquipmentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EquipmentRepositoryInterface::class, EquipmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
