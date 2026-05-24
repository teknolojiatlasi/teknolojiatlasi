<?php

namespace App\Providers;

use App\Support\SeoService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SeoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.force_https') && ! app()->environment(['local', 'testing'])) {
            URL::forceScheme('https');
        }

        View::addNamespace('app', resource_path('views'));
        View::composer('*', function ($view): void {
            $view->with('seo', app(SeoService::class)->forCurrentRequest());
        });
    }
}
