<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibrarySync
{
    private const ALLOWED_MEDIA_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf', 'mp4', 'mov',
    ];

    private const IMAGE_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg',
    ];

    public function __construct(
        private readonly WordPressSqlDumpReader $dumpReader,
    ) {}

    /**
     * Synchronize images from existing database records, public disk files, and an optional WordPress SQL backup.
     *
     * @return array{added: int, from_content: int, from_storage: int, from_wp_dump: int, skipped: int}
     */
    public function sync(?string $wpDumpPath = null): array
    {
        $existing = [];
        foreach (Media::query()->pluck('file_path') as $existingPath) {
            $normalized = MediaUrl::normalizeForStorage($existingPath) ?? trim((string) $existingPath);
            if ($normalized !== '') {
                $existing[mb_strtolower($normalized)] = true;
            }
        }

        $pending = [];
        $stats = [
            'added' => 0,
            'from_content' => 0,
            'from_storage' => 0,
            'from_wp_dump' => 0,
            'skipped' => 0,
        ];

        $this->collectFromContentTables($existing, $pending, $stats);
        $this->collectFromPublicDisk($existing, $pending, $stats);

        if (filled($wpDumpPath) && is_file($wpDumpPath)) {
            $this->collectFromWordPressDump($wpDumpPath, $existing, $pending, $stats);
        }

        if ($pending !== []) {
            DB::transaction(function () use ($pending): void {
                foreach (array_chunk($pending, 250) as $chunk) {
                    Media::query()->insert($chunk);
                }
            });
            $stats['added'] = count($pending);
        }

        return $stats;
    }

    /**
     * Register a single image reference in the media library if not already indexed.
     */
    public function ensureIndexed(?string $rawPath, ?string $caption = null, ?string $mimeType = null, ?CarbonInterface $timestamp = null): ?Media
    {
        $candidate = $this->buildCandidate($rawPath, $caption, $mimeType, $timestamp);
        if ($candidate === null) {
            return null;
        }

        $existing = Media::query()->where('file_path', $candidate['file_path'])->first();
        if ($existing !== null) {
            return $existing;
        }

        $media = new Media;
        $media->forceFill($candidate);
        $media->save();

        return $media;
    }

    /**
     * @param  array<string, bool>  $existing
     * @param  list<array<string, mixed>>  $pending
     * @param  array<string, int>  $stats
     */
    private function collectFromContentTables(array &$existing, array &$pending, array &$stats): void
    {
        Article::query()
            ->select(['id', 'title', 'featured_image', 'content', 'published_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($articles) use (&$existing, &$pending, &$stats): void {
                foreach ($articles as $article) {
                    $timestamp = $article->published_at ?? $article->created_at;
                    $this->queueCandidate(
                        $article->featured_image,
                        $article->title,
                        null,
                        $timestamp,
                        'from_content',
                        $existing,
                        $pending,
                        $stats
                    );

                    foreach ($this->extractImagesFromHtml($article->content) as $inlineUrl) {
                        $this->queueCandidate(
                            $inlineUrl,
                            $article->title,
                            null,
                            $timestamp,
                            'from_content',
                            $existing,
                            $pending,
                            $stats
                        );
                    }
                }
            });

        Event::query()
            ->select(['id', 'title', 'image', 'starts_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($events) use (&$existing, &$pending, &$stats): void {
                foreach ($events as $event) {
                    $this->queueCandidate(
                        $event->image,
                        $event->title,
                        null,
                        $event->created_at ?? $event->starts_at,
                        'from_content',
                        $existing,
                        $pending,
                        $stats
                    );
                }
            });

        Expert::query()
            ->select(['id', 'name', 'photo', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($experts) use (&$existing, &$pending, &$stats): void {
                foreach ($experts as $expert) {
                    $this->queueCandidate(
                        $expert->photo,
                        $expert->name,
                        null,
                        $expert->created_at,
                        'from_content',
                        $existing,
                        $pending,
                        $stats
                    );
                }
            });

        Product::query()
            ->select(['id', 'name', 'image', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($products) use (&$existing, &$pending, &$stats): void {
                foreach ($products as $product) {
                    $this->queueCandidate(
                        $product->image,
                        $product->name,
                        null,
                        $product->created_at,
                        'from_content',
                        $existing,
                        $pending,
                        $stats
                    );
                }
            });

        Category::query()
            ->select(['id', 'name', 'hero_image', 'created_at'])
            ->orderBy('id')
            ->chunkById(200, function ($categories) use (&$existing, &$pending, &$stats): void {
                foreach ($categories as $category) {
                    $this->queueCandidate(
                        $category->hero_image,
                        $category->name,
                        null,
                        $category->created_at,
                        'from_content',
                        $existing,
                        $pending,
                        $stats
                    );
                }
            });
    }

    /**
     * @param  array<string, bool>  $existing
     * @param  list<array<string, mixed>>  $pending
     * @param  array<string, int>  $stats
     */
    private function collectFromPublicDisk(array &$existing, array &$pending, array &$stats): void
    {
        $disk = Storage::disk('public');

        foreach ($disk->allFiles() as $filePath) {
            $basename = basename($filePath);
            if ($basename === '' || str_starts_with($basename, '.')) {
                continue;
            }

            $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
            if (! in_array($extension, self::ALLOWED_MEDIA_EXTENSIONS, true)) {
                continue;
            }

            $timestamp = null;
            try {
                $timestamp = Carbon::createFromTimestamp($disk->lastModified($filePath));
            } catch (\Throwable) {
                $timestamp = null;
            }

            $this->queueCandidate(
                $filePath,
                null,
                null,
                $timestamp,
                'from_storage',
                $existing,
                $pending,
                $stats
            );
        }
    }

    /**
     * @param  array<string, bool>  $existing
     * @param  list<array<string, mixed>>  $pending
     * @param  array<string, int>  $stats
     */
    private function collectFromWordPressDump(string $wpDumpPath, array &$existing, array &$pending, array &$stats): void
    {
        $prefix = $this->dumpReader->detectPrefix($wpDumpPath);

        foreach ($this->dumpReader->rows($wpDumpPath, $prefix, ['posts']) as $row) {
            $fields = $row['fields'];
            if (($fields['post_type'] ?? '') !== 'attachment') {
                continue;
            }

            $mimeType = trim((string) ($fields['post_mime_type'] ?? ''));
            if (! str_starts_with($mimeType, 'image/')) {
                continue;
            }

            $guid = trim((string) ($fields['guid'] ?? ''));
            if ($guid === '') {
                continue;
            }

            $title = trim(strip_tags((string) ($fields['post_title'] ?? '')));
            $excerpt = trim(strip_tags((string) ($fields['post_excerpt'] ?? '')));
            $caption = $excerpt !== '' ? $excerpt : ($title !== '' ? $title : null);
            $timestamp = $this->parseWordPressDate($fields['post_date_gmt'] ?? $fields['post_date'] ?? null);

            $this->queueCandidate(
                $guid,
                $caption,
                $mimeType,
                $timestamp,
                'from_wp_dump',
                $existing,
                $pending,
                $stats,
                $title !== '' ? $title : null
            );
        }
    }

    /**
     * @param  array<string, bool>  $existing
     * @param  list<array<string, mixed>>  $pending
     * @param  array<string, int>  $stats
     */
    private function queueCandidate(
        ?string $rawPath,
        ?string $caption,
        ?string $mimeType,
        ?CarbonInterface $timestamp,
        string $counterKey,
        array &$existing,
        array &$pending,
        array &$stats,
        ?string $preferredName = null,
    ): void {
        $candidate = $this->buildCandidate($rawPath, $caption, $mimeType, $timestamp, $preferredName);

        if ($candidate === null) {
            if (filled($rawPath)) {
                $stats['skipped']++;
            }

            return;
        }

        $lookupKey = mb_strtolower($candidate['file_path']);
        if (isset($existing[$lookupKey])) {
            return;
        }

        $existing[$lookupKey] = true;
        $pending[] = $candidate;
        $stats[$counterKey]++;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildCandidate(
        ?string $rawPath,
        ?string $caption = null,
        ?string $mimeType = null,
        ?CarbonInterface $timestamp = null,
        ?string $preferredName = null,
    ): ?array {
        if (blank($rawPath)) {
            return null;
        }

        $normalized = MediaUrl::normalizeForStorage($rawPath);
        if ($normalized === null || $normalized === '' || Str::length($normalized) > 255) {
            return null;
        }

        if (Str::startsWith($normalized, ['data:', 'blob:']) || Str::contains($normalized, ['..', '\\'])) {
            return null;
        }

        $isRemote = Str::startsWith($normalized, ['http://', 'https://', '//']);
        $disk = Storage::disk('public');

        $size = 0;
        $width = null;
        $height = null;

        if (! $isRemote) {
            if (! $disk->exists($normalized)) {
                return null;
            }

            try {
                $size = (int) ($disk->size($normalized) ?: 0);
            } catch (\Throwable) {
                $size = 0;
            }

            $mimeType ??= $this->detectLocalMimeType($normalized);

            if (Str::startsWith((string) $mimeType, 'image/')) {
                try {
                    $fullPath = $disk->path($normalized);
                    if (is_file($fullPath)) {
                        $dimensions = @getimagesize($fullPath);
                        if (is_array($dimensions)) {
                            $width = $dimensions[0] ?? null;
                            $height = $dimensions[1] ?? null;
                        }
                    }
                } catch (\Throwable) {
                    // Ignore unreadable dimensions
                }
            }
        } else {
            $urlPath = (string) (parse_url($normalized, PHP_URL_PATH) ?? '');
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
            if ($mimeType === null && ! in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                return null;
            }

            $mimeType ??= $this->mimeFromExtension($extension);
        }

        $pathForName = $isRemote ? (string) (parse_url($normalized, PHP_URL_PATH) ?? '') : $normalized;
        $filename = basename($pathForName);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            $filename = 'asset.jpg';
        }

        $name = filled($preferredName) ? $preferredName : $filename;
        $createdAt = ($timestamp ?? now())->format('Y-m-d H:i:s');

        return [
            'name' => Str::limit(trim($name), 255, ''),
            'file_path' => $normalized,
            'mime_type' => $mimeType ? Str::limit($mimeType, 255, '') : 'image/jpeg',
            'size' => max(0, $size),
            'width' => $width,
            'height' => $height,
            'caption' => filled($caption) ? Str::limit(trim((string) $caption), 255, '') : null,
            'alt_text' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    /**
     * @return list<string>
     */
    private function extractImagesFromHtml(?string $html): array
    {
        if ($html === null || $html === '' || ! str_contains($html, '<img')) {
            return [];
        }

        preg_match_all('/<img\b[^>]*\bsrc\s*=\s*(["\'])([^"\']+)\1/i', $html, $matches);

        return array_values(array_filter($matches[2] ?? []));
    }

    private function detectLocalMimeType(string $path): string
    {
        try {
            $mime = Storage::disk('public')->mimeType($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        } catch (\Throwable) {
            // Fallback to extension detection
        }

        return $this->mimeFromExtension(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
    }

    private function mimeFromExtension(string $extension): string
    {
        return match ($extension) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            default => 'image/jpeg',
        };
    }

    private function parseWordPressDate(?string $date): ?Carbon
    {
        if (blank($date) || str_starts_with($date, '0000-00-00')) {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('!Y-m-d H:i:s', $date, 'UTC');

            return $parsed === false ? null : $parsed->setTimezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }
}
