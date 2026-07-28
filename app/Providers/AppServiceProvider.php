<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
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

        // Share joined groups with the sidebar menu safely
        View::composer('*', function ($view) {
            if (Auth::check() && Schema::hasTable('group_discussions')) {
                try {
                    $joinedGroups = GroupDiscussion::whereHas('members', function ($query) {
                        $query->where('users.id', Auth::id());
                    })->get();
                    
                    $view->with('sidebarGroups', $joinedGroups);
                } catch (\Exception $e) {
                    $view->with('sidebarGroups', collect());
                }
            } else {
                $view->with('sidebarGroups', collect());
            }
        });
    }
}