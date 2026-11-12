<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasImageUrl
{
    /**
     * Resolve a stored image value (remote URL or public-disk path) into a URL.
     */
    protected function resolveImageUrl(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}
