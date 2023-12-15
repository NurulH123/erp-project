<?php

namespace App\Providers;

use App\Http\Controllers\Auth\AuthController;
use App\Repository\Auth\LoginRepositoryImplement;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(LoginRepositoryImplement::class, AuthController::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
