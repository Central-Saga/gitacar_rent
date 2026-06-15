<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/livewire/pages'), 'pages');
        $this->configureDefaults();

        $this->configureHttps();
        $this->disableUnusedVitePreloads();
    }

    /**
     * Ensure URLs are generated with the correct scheme and root URL
     * when running behind a reverse proxy (Dokploy/Traefik/Nginx).
     *
     * This is critical for Livewire file uploads, which use signed
     * temporary URLs. If APP_URL is not explicitly set to the real
     * domain, the signature will mismatch and return 401.
     */
    protected function configureHttps(): void
    {
        $request = request();

        $isSecure = $request?->isSecure()
            || $request?->header('X-Forwarded-Proto') === 'https'
            || $request?->server('HTTPS') === 'on'
            || app()->isProduction();

        if ($isSecure) {
            URL::forceScheme('https');
        }

        $appUrl = config('app.url');

        // In production, always derive the root URL from the actual request
        // when APP_URL is still the default (http://localhost). This ensures
        // signed URLs (e.g. Livewire file uploads) use the real domain.
        if (app()->isProduction() && $appUrl === 'http://localhost') {
            $scheme = $isSecure ? 'https' : 'http';
            $host = $request?->getHost() ?? 'localhost';
            URL::forceRootUrl("{$scheme}://{$host}");
        } elseif ($appUrl && $appUrl !== 'http://localhost' && ! app()->runningUnitTests()) {
            URL::forceRootUrl($appUrl);
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    /**
     * Disable Vite preload hints for the empty app.js entry to prevent
     * Chrome warnings about preloaded resources that are never consumed.
     */
    protected function disableUnusedVitePreloads(): void
    {
        Vite::usePreloadTagAttributes(
            fn (string $src, string $url, array $chunk, array $manifest): false => false
        );
    }
}
