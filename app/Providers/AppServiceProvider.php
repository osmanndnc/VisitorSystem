<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        mb_internal_encoding("UTF-8");

        // Global Context ekle.
        app()->singleton('request_uid', fn() => (string) Str::uuid());

        Log::withContext([
            'request_id' => app('request_uid'),
            'ip'       => request()->ip(),
            'url'      => request()->fullUrl(),
            'user_id'  => auth()->id(),
            'username' => optional(auth()->user())->username,
        ]);
    }
}
