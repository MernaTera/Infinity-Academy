<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\LeadRepositoryInterface;
use App\Repositories\LeadRepository;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind (
            LeadRepositoryInterface::class,
            LeadRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::if('cando', function (string $permission) {
            return auth()->check() && auth()->user()->canDo($permission);
        });
    }
}
