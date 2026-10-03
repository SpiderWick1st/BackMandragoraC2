<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Implementations\EloquentReservaRepository;
use App\Repositories\Interfaces\ReservaRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReservaRepositoryInterface::class, EloquentReservaRepository::class);
    }

    public function boot(): void {}
}
