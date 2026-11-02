<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        $host = request()->getHost();
        $isExternalDomain = ! in_array($host, ['localhost', '127.0.0.1']) && ! str_starts_with($host, '192.168.');

        if (
            request()->header('x-forwarded-proto') === 'https'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->isSecure()
            || $isExternalDomain
        ) {
            URL::forceScheme('https');
        }

        // Prevent broken layout/styles caused by stale Vite "hot" reload file when dev server is closed
        if (file_exists(public_path('hot'))) {
            $hotUrl = trim((string) @file_get_contents(public_path('hot')));
            $parts = parse_url($hotUrl);
            $host = $parts['host'] ?? '127.0.0.1';
            if ($host === '[::1]' || $host === '::1') {
                $host = '127.0.0.1';
            }
            $port = (int) ($parts['port'] ?? 5173);

            $socket = @fsockopen($host, $port, $errno, $errstr, 0.05);
            if (! is_resource($socket)) {
                @unlink(public_path('hot'));
            } else {
                fclose($socket);
            }
        }
    }
}
