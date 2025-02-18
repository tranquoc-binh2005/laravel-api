<?php

namespace App\Providers;

use App\Services\Impl\Auth\AuthService;
use App\Services\Impl\Mail\MailResetPasswordService;

use App\Services\Interfaces\Auth\AuthServiceInterface;
use App\Services\Interfaces\Mail\MailResetPasswordServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(MailResetPasswordServiceInterface::class, MailResetPasswordService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
