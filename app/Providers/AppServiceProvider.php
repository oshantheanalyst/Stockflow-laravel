<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    // Register any application services.
    public function register(): void
    {
        //
    }

    // Bootstrap any application services.
    public function boot(): void
    {
        // Override Jetstream's browser sessions component with our API tokens component
        Livewire::component('profile.logout-other-browser-sessions-form', \App\Livewire\LogoutOtherBrowserSessionsForm::class);

        // Custom session guard removed to enforce strict API-dependency.

        // Failsafe: RequestGuard (returned by Auth::viaRequest) does not support viaRemember() out of the box.
        // If any vendor middleware or package tries to call viaRemember() on our api_session guard, 
        // this macro intercepts it gracefully and returns false, preventing BadMethodCallException.
        if (class_exists(\Illuminate\Auth\RequestGuard::class)) {
            \Illuminate\Auth\RequestGuard::macro('viaRemember', function () {
                return false;
            });
        }

        if (! $this->app->runningInConsole() && $this->app->bound('request')) {
            $currentUrl = request()->getSchemeAndHttpHost() . request()->getBasePath();
            config(['app.url' => $currentUrl]);
            config(['app.asset_url' => $currentUrl]);
            
            // Dynamically override Google OAuth redirect callback to support subdirectory paths on XAMPP and artisan serve
            config(['services.google.redirect' => $currentUrl . '/api/auth/google/callback']);

            $basePath = request()->getBasePath();

            Livewire::setScriptRoute(function ($handle) use ($basePath) {
                return Route::get($basePath . '/livewire/livewire.js', $handle);
            });

            Livewire::setUpdateRoute(function ($handle) use ($basePath) {
                return Route::post($basePath . '/livewire/update', $handle);
            });
        }
    }
}
