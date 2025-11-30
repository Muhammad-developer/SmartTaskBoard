<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
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
        Task::observe(TaskObserver::class);

        // Set user's locale from session or database (only in web context)
        if ($this->app->runningInConsole() === false) {
            try {
                $locale = session()->get('locale') ?? (auth()->check() ? auth()->user()->locale : 'en');
                app()->setLocale($locale);
            } catch (\Exception $e) {
                // Session not available, use default locale
                app()->setLocale('en');
            }
        }
    }
}
