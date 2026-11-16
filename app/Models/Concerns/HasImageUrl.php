<?php

namespace App\Models\Concerns;

use App\Support\MediaUrl;

trait HasImageUrl
{
    /**
     * Resolve a stored image value (remote URL or public-disk path) into a URL.
     */
    protected function resolveImageUrl(?string $value): ?string
    {
        return MediaUrl::resolve($value);
    }
}
