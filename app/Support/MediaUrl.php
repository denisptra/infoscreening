<?php

namespace App\Support;

use Illuminate\Support\Str;

class MediaUrl
{
    /**
     * Resolve a stored image path or URL into a browser-accessible URL.
     */
    public static function resolve(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $trimmed = trim($value);

        if (Str::startsWith($trimmed, ['data:', 'blob:'])) {
            return $trimmed;
        }

        if (($storagePath = self::extractLocalStoragePath($trimmed)) !== null) {
            return '/storage/'.ltrim($storagePath, '/');
        }

        if (Str::startsWith($trimmed, ['http://', 'https://', '//', '/'])) {
            return $trimmed;
        }

        $normalized = str_replace('\\', '/', $trimmed);

        if (Str::startsWith($normalized, 'public/')) {
            $normalized = substr($normalized, strlen('public/'));
        }

        return '/storage/'.ltrim($normalized, '/');
    }

    /**
     * Normalize an image input before persisting to the database.
     * Local/loopback storage URLs are converted to disk-relative paths; external URLs stay intact.
     */
    public static function normalizeForStorage(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $trimmed = trim($value);

        if (($storagePath = self::extractLocalStoragePath($trimmed)) !== null) {
            return ltrim($storagePath, '/');
        }

        return $trimmed;
    }

    /**
     * Normalize a stored value for Filament ImageColumn configured with disk('public').
     */
    public static function normalizeForFilamentColumn(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $trimmed = trim($value);

        if (($storagePath = self::extractLocalStoragePath($trimmed)) !== null) {
            return ltrim($storagePath, '/');
        }

        if (Str::startsWith($trimmed, ['http://', 'https://', 'data:'])) {
            return $trimmed;
        }

        return ltrim(str_replace('\\', '/', $trimmed), '/');
    }

    /**
     * Rewrite local/private-host /storage/... URLs embedded in rich-text HTML into relative /storage/... paths.
     */
    public static function normalizeHtml(?string $html): ?string
    {
        if ($html === null || $html === '' || ! str_contains($html, '/storage/')) {
            return $html;
        }

        return preg_replace_callback(
            '#\b(src|href)\s*=\s*(["\'])((?:https?:)?//[^"\'\s>]+/storage/[^"\'\s>]*)\2#i',
            function (array $matches): string {
                $url = $matches[3];
                $storagePath = self::extractLocalStoragePath($url);

                if ($storagePath === null) {
                    return $matches[0];
                }

                return $matches[1].'='.$matches[2].'/storage/'.ltrim($storagePath, '/').$matches[2];
            },
            $html
        ) ?? $html;
    }

    /**
     * Determine whether a hostname points to localhost, loopback, private LAN, or local dev domains.
     */
    public static function isLocalOrPrivateHost(?string $host): bool
    {
        if (blank($host)) {
            return false;
        }

        $normalized = strtolower(trim($host, "[] \t\n\r\0\x0B"));

        if (in_array($normalized, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)) {
            return true;
        }

        if (Str::endsWith($normalized, ['.localhost', '.local', '.test', '.internal'])) {
            return true;
        }

        if (filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if (filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }

            // Carrier-grade NAT / Tailscale (100.64.0.0/10)
            $long = ip2long($normalized);
            if ($long !== false && ($long & 0xFFC00000) === 0x64400000) {
                return true;
            }
        }

        if (filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        return false;
    }

    /**
     * Extract the public-disk-relative path when the value is a local /storage/... reference or URL.
     */
    public static function extractLocalStoragePath(string $value): ?string
    {
        $normalized = str_replace('\\', '/', trim($value));

        if (str_contains($normalized, '..')) {
            return null;
        }

        foreach (['/public/storage/', 'public/storage/', '/storage/', 'storage/'] as $prefix) {
            if (Str::startsWith($normalized, $prefix)) {
                $relative = substr($normalized, strlen($prefix));

                return $relative !== '' ? ltrim($relative, '/') : null;
            }
        }

        if (preg_match('#^(?:https?:)?//([^/]+)(?::\d+)?/(?:public/)?storage/(.+)$#i', $normalized, $matches)) {
            $urlHost = $matches[1];
            // Strip port when IPv6 bracketed or host:port
            $parsedHost = parse_url(Str::startsWith($normalized, '//') ? 'http:'.$normalized : $normalized, PHP_URL_HOST) ?: $urlHost;

            if (self::isAppOrLocalHost($parsedHost)) {
                return ltrim($matches[2], '/');
            }
        }

        return null;
    }

    private static function isAppOrLocalHost(?string $host): bool
    {
        if (self::isLocalOrPrivateHost($host)) {
            return true;
        }

        $normalizedHost = strtolower(trim((string) $host, '[]'));
        if ($normalizedHost === '') {
            return false;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && strtolower(trim($appHost, '[]')) === $normalizedHost) {
            return true;
        }

        if (! app()->runningInConsole() && app()->bound('request')) {
            $requestHost = request()->getHost();
            if ($requestHost !== '' && strtolower(trim($requestHost, '[]')) === $normalizedHost) {
                return true;
            }
        }

        return false;
    }
}
