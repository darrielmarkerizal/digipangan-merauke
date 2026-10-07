<?php

namespace App\Providers;

use App\Repositories\AuditRepository;
use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);

        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        RateLimiter::for('password-reset-request', fn (Request $request) => Limit::perMinute(3)
            ->by($request->ip())
            ->response(fn (Request $request, array $headers) => $request->expectsJson()
                ? response()->json(['message' => 'Terlalu banyak permintaan reset. Coba kembali sebentar lagi.'], 429, $headers)
                : back()->withErrors(['email' => 'Terlalu banyak permintaan reset. Coba kembali sebentar lagi.'])
                    ->withInput($request->only('email'))));

        RateLimiter::for('password-reset-update', fn (Request $request) => Limit::perMinute(5)
            ->by($request->ip())
            ->response(fn (Request $request, array $headers) => $request->expectsJson()
                ? response()->json(['message' => 'Terlalu banyak percobaan. Coba kembali sebentar lagi.'], 429, $headers)
                : back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba kembali sebentar lagi.'])
                    ->withInput($request->only('email'))));
    }
}
