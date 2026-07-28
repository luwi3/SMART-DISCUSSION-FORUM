<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\GroupDiscussion;

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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Share joined groups with the sidebar menu globally across all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $joinedGroups = GroupDiscussion::whereHas('members', function ($query) {
                    $query->where('users.id', Auth::id());
                })->get();
                
                $view->with('sidebarGroups', $joinedGroups);
            }
        });
    }
}