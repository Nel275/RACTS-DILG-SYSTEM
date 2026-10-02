<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Railway serves the site over HTTPS - make every generated URL
        // (form actions, redirects, assets) use https:// in production.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Laravel's default pagination views use Tailwind CSS classes.
        // RACTS uses Bootstrap only, so render pagination links with
        // Bootstrap 5 markup instead.
        Paginator::useBootstrapFive();

        // Share the logged-in user's unread notification count with the
        // main layout, so the "Notifications" nav badge stays up to date
        // on every page without every controller having to pass it in.
        View::composer('layouts.app', function ($view) {
            $view->with(
                'unreadNotificationsCount',
                Auth::check() ? Auth::user()->notifications()->unread()->count() : 0
            );
        });
    }
}
