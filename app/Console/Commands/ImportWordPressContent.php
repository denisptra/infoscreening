<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\User;
use App\Support\WordPressSqlDumpReader;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('app:import-wordpress-content {dump : Path ke file SQL WordPress} {--author-email= : Email Super Admin untuk kepemilikan artikel hasil impor}')]
#[Description('Pindahkan artikel, kategori, tag, dan event dari dump WordPress ke CMS Infoscreening')]
class ImportWordPressContent extends Command
{
    private const CONTENT_META_KEYS = [
        '_thumbnail_id',
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        'post_views_count',
        '_EventStartDate',
        '_EventEndDate',
        '_EventStartDateUTC',
        '_EventEndDateUTC',
        '_EventURL',
        '_EventVenueID',
        '_EventTimezone',
        '_EventAllDay',
        '_VenueAddress',
        '_VenueCity',
        '_VenueProvince',
    ];

    /**
     * @var array<int, array<string, string|null>>
     */
    private array $posts = [];

    /**
     * @var array<int, array{name: string, slug: string, description: string}>
     */
    private array $terms = [];

    /**
     * @var array<int, array{term_id: int, taxonomy: string}>
     */
    private array $taxonomies = [];

    /**
     * @var array<int, list<int>>
     */
    private array $relationships = [];

    /**
     * @var array<int, string>
     */
    private array $authors = [];

    /**
     * @var array<int, array<string, string|null>>
     */
    private array $metadata = [];

    public function handle(WordPressSqlDumpReader $reader): int
    {
        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true) && ! app()->runningUnitTests()) {
            $this->components->error('Database Laravel belum menggunakan MySQL. Atur DB_CONNECTION=mysql lalu jalankan migrasi terlebih dahulu.');

            return self::FAILURE;
        }

        if (app()->runningUnitTests() && ! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $this->components->error('Database Laravel belum menggunakan MySQL. Atur DB_CONNECTION=mysql lalu jalankan migrasi terlebih dahulu.');

            return self::FAILURE;
        }

        $dumpPath = $this->argument('dump');

        if (! is_file($dumpPath) || ! is_readable($dumpPath)) {
            $this->components->error('File dump tidak ditemukan atau tidak dapat dibaca.');

            return self::FAILURE;
        }

        $authorQuery = User::query()->where('role', UserRole::SuperAdmin->value);
        if (filled($this->option('author-email'))) {
            $authorQuery->where('email', $this->option('author-email'));
        }
        $author = $authorQuery->first();

        if ($author === null) {
            $this->components->error('Akun Super Admin tidak ditemukan. Daftarkan akun, lalu jalankan app:promote-super-admin terlebih dahulu.');

            return self::FAILURE;
        }

        $prefix = $reader->detectPrefix($dumpPath);
        $this->loadSourceRecords($reader, $dumpPath, $prefix);
        $this->loadSourceMetadata($reader, $dumpPath, $prefix);

        $results = [
            'articles' => 0,
            'events' => 0,
            'categories' => 0,
            'tags' => 0,
            'skipped_events' => 0,
            'skipped_articles' => 0,
        ];

        DB::transaction(function () use ($author, &$results): void {
            $categoryIds = $this->importTerms('category', Category::class, $results);
            $tagIds = $this->importTerms('post_tag', Tag::class, $results);

            foreach ($this->posts as $sourceId => $post) {
                if ($post['post_type'] === 'post') {
                    if ($this->importArticle($sourceId, $post, $author, $categoryIds, $tagIds)) {
                        $results['articles']++;
                    } else {
                        $results['skipped_articles']++;
                    }
                } elseif ($post['post_type'] === 'tribe_events') {
                    if ($this->importEvent($sourceId, $post)) {
                        $results['events']++;
                    } else {
                        $results['skipped_events']++;
                    }
                }
            }
        });

        $this->components->info(sprintf(
            'Impor selesai: %s artikel, %s event, %s kategori, %s tag. %s artikel dan %s event dilewati karena data utama tidak valid.',
            number_format($results['articles']),
            number_format($results['events']),
            number_format($results['categories']),
            number_format($results['tags']),
            number_format($results['skipped_articles']),
            number_format($results['skipped_events'])
        ));

        return self::SUCCESS;
    }

    private function loadSourceRecords(WordPressSqlDumpReader $reader, string $path, string $prefix): void
    {
        foreach ($reader->rows($path, $prefix, ['posts', 'terms', 'term_taxonomy', 'term_relationships', 'users']) as $row) {
            $fields = $row['fields'];

            if ($row['table'] === 'posts') {
                $type = $fields['post_type'] ?? '';
                $status = $fields['post_status'] ?? '';
                $isContent = in_array($type, ['post', 'tribe_events'], true)
                    && in_array($status, ['publish', 'draft'], true);
                $isSupportingRecord = in_array($type, ['tribe_venue', 'attachment'], true)
                    && in_array($status, ['publish', 'draft', 'inherit'], true);

                if (! $isContent && ! $isSupportingRecord) {
                    continue;
                }

                $this->posts[(int) $fields['ID']] = $fields;
            } elseif ($row['table'] === 'terms') {
                $termId = (int) $fields['term_id'];
                $this->terms[$termId] = [
                    'name' => (string) $fields['name'],
                    'slug' => (string) $fields['slug'],
                    'description' => '',
                ];
            } elseif ($row['table'] === 'term_taxonomy') {
                $this->taxonomies[(int) $fields['term_taxonomy_id']] = [
                    'term_id' => (int) $fields['term_id'],
                    'taxonomy' => (string) $fields['taxonomy'],
                ];
                if (isset($this->terms[(int) $fields['term_id']])) {
                    $this->terms[(int) $fields['term_id']]['description'] = (string) $fields['description'];
                }
            } elseif ($row['table'] === 'term_relationships') {
                $this->relationships[(int) $fields['object_id']][] = (int) $fields['term_taxonomy_id'];
            } elseif ($row['table'] === 'users' && filled($fields['display_name'] ?? null)) {
                $this->authors[(int) $fields['ID']] = (string) $fields['display_name'];
            }
        }
    }

    private function loadSourceMetadata(WordPressSqlDumpReader $reader, string $path, string $prefix): void
    {
        $postIds = array_fill_keys(array_keys($this->posts), true);

        foreach ($reader->rows($path, $prefix, ['postmeta']) as $row) {
            $fields = $row['fields'];
            $postId = (int) $fields['post_id'];
            $metaKey = (string) $fields['meta_key'];

            if (isset($postIds[$postId]) && in_array($metaKey, self::CONTENT_META_KEYS, true)) {
                $this->metadata[$postId][$metaKey] = $fields['meta_value'];
            }
        }
    }

    /**
     * @param  class-string<Category|Tag>  $modelClass
     * @param  array<string, int>  $results
     * @return array<int, int>
     */
    private function importTerms(string $taxonomy, string $modelClass, array &$results): array
    {
        $termIds = [];

        foreach ($this->posts as $postId => $post) {
            if ($post['post_type'] !== 'post') {
                continue;
            }

            foreach ($this->relationships[$postId] ?? [] as $taxonomyId) {
                $taxonomyData = $this->taxonomies[$taxonomyId] ?? null;
                if ($taxonomyData === null || $taxonomyData['taxonomy'] !== $taxonomy) {
                    continue;
                }

                $termId = $taxonomyData['term_id'];
                if (isset($termIds[$termId])) {
                    continue;
                }

                $term = $this->terms[$termId] ?? null;
                if ($term === null || blank($term['name'])) {
                    continue;
                }

                $slug = Str::slug($term['slug'] ?: $term['name']);
                if ($slug === '') {
                    continue;
                }

                $model = $modelClass::query()->updateOrCreate(
                    ['slug' => Str::limit($slug, 220, '')],
                    [
                        'name' => Str::limit($term['name'], 100, ''),
                        'description' => $term['description'] !== ''
                            ? Str::limit($term['description'], 500, '')
                            : null,
                    ]
                );
                $termIds[$termId] = $model->id;
                $results[$taxonomy === 'category' ? 'categories' : 'tags']++;
            }
        }

        return $termIds;
    }

    /**
     * @param  array<string, string|null>  $post
     * @param  array<int, int>  $categoryIds
     * @param  array<int, int>  $tagIds
     */
    private function importArticle(int $sourceId, array $post, User $author, array $categoryIds, array $tagIds): bool
    {
        $postStatus = $post['post_status'] === 'publish' ? 'published' : 'draft';
        $publishedAt = $postStatus === 'published'
            ? $this->parseUtcDate($post['post_date_gmt'] ?: $post['post_date'])
            : null;

        if ($postStatus === 'published' && $publishedAt === null) {
            return false;
        }

        $metadata = $this->metadata[$sourceId] ?? [];
        $title = trim(strip_tags((string) $post['post_title']));
        if ($title === '') {
            return false;
        }

        $content = $this->plainText((string) $post['post_content']);
        $excerpt = $this->plainText((string) $post['post_excerpt']);
        $article = Article::query()->updateOrCreate(
            ['wordpress_source_id' => $sourceId],
            [
                'user_id' => $author->id,
                'legacy_author_name' => isset($this->authors[(int) $post['post_author']])
                    ? Str::limit($this->authors[(int) $post['post_author']], 255, '')
                    : null,
                'title' => Str::limit($title, 200, ''),
                'slug' => $this->uniqueArticleSlug((string) $post['post_name'], $title, $sourceId),
                'excerpt' => $excerpt !== '' ? Str::limit($excerpt, 500, '') : null,
                'content' => $content,
                'status' => $postStatus,
                'published_at' => $publishedAt,
                'featured_image' => $this->attachmentUrl($metadata['_thumbnail_id'] ?? null),
                'meta_title' => filled($metadata['_yoast_wpseo_title'] ?? null)
                    ? Str::limit($metadata['_yoast_wpseo_title'], 200, '')
                    : null,
                'meta_description' => filled($metadata['_yoast_wpseo_metadesc'] ?? null)
                    ? Str::limit($metadata['_yoast_wpseo_metadesc'], 300, '')
                    : null,
                'views' => max(0, (int) ($metadata['post_views_count'] ?? 0)),
                'reading_time' => max(1, (int) ceil(str_word_count($content) / 200)),
                'is_featured' => false,
            ]
        );

        $article->categories()->sync($this->relatedTermIds($sourceId, 'category', $categoryIds));
        $article->tags()->sync($this->relatedTermIds($sourceId, 'post_tag', $tagIds));

        return true;
    }

    /**
     * @param  array<string, string|null>  $post
     */
    private function importEvent(int $sourceId, array $post): bool
    {
        $metadata = $this->metadata[$sourceId] ?? [];
        $timezone = (string) ($metadata['_EventTimezone'] ?? 'UTC');
        $startsAt = $this->parseEventDate(
            $metadata['_EventStartDate'] ?? $metadata['_EventStartDateUTC'] ?? null,
            $timezone
        );

        if ($startsAt === null) {
            return false;
        }

        $endsAt = $this->parseEventDate(
            $metadata['_EventEndDate'] ?? $metadata['_EventEndDateUTC'] ?? null,
            $timezone
        );
        $title = trim(strip_tags((string) $post['post_title']));
        if ($title === '') {
            return false;
        }

        $venueId = (int) ($metadata['_EventVenueID'] ?? 0);
        $venue = $this->posts[$venueId] ?? null;
        $venueMetadata = $this->metadata[$venueId] ?? [];
        $venueName = $venue === null ? null : trim(strip_tags((string) $venue['post_title']));
        $venueAddress = trim((string) ($venueMetadata['_VenueAddress'] ?? ''));
        $venueText = implode(', ', array_filter([$venueName, $venueAddress]));
        $city = $venueMetadata['_VenueCity'] ?? $venueMetadata['_VenueProvince'] ?? null;

        Event::query()->updateOrCreate(
            ['wordpress_source_id' => $sourceId],
            [
                'title' => Str::limit($title, 200, ''),
                'slug' => $this->uniqueEventSlug((string) $post['post_name'], $title, $sourceId),
                'type' => $this->eventType($sourceId),
                'description' => $this->plainText((string) $post['post_content']),
                'venue' => $venueText !== '' ? Str::limit($venueText, 160, '') : null,
                'city' => filled($city) ? Str::limit($city, 100, '') : null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'image' => $this->attachmentUrl($metadata['_thumbnail_id'] ?? null),
                'url' => $this->safeUrl($metadata['_EventURL'] ?? null),
                'is_published' => $post['post_status'] === 'publish',
            ]
        );

        return true;
    }

    private function eventType(int $postId): string
    {
        $typeTerms = [
            'Festival' => ['festival'],
            'Pemutaran' => ['pemutaran', 'screening'],
            'Diskusi' => ['diskusi', 'discussion', 'talk'],
            'Workshop' => ['workshop', 'lokakarya'],
            'Pameran' => ['pameran', 'exhibition'],
        ];

        foreach ($this->relationships[$postId] ?? [] as $taxonomyId) {
            $taxonomy = $this->taxonomies[$taxonomyId] ?? null;
            if ($taxonomy === null || $taxonomy['taxonomy'] !== 'tribe_events_cat') {
                continue;
            }

            $termName = Str::lower($this->terms[$taxonomy['term_id']]['name'] ?? '');

            foreach ($typeTerms as $type => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($termName, $keyword)) {
                        return $type;
                    }
                }
            }
        }

        return 'Lainnya';
    }

    /**
     * @param  array<int, int>  $termIds
     * @return list<int>
     */
    private function relatedTermIds(int $postId, string $taxonomy, array $termIds): array
    {
        $relatedIds = [];

        foreach ($this->relationships[$postId] ?? [] as $taxonomyId) {
            $taxonomyData = $this->taxonomies[$taxonomyId] ?? null;
            if ($taxonomyData !== null && $taxonomyData['taxonomy'] === $taxonomy && isset($termIds[$taxonomyData['term_id']])) {
                $relatedIds[] = $termIds[$taxonomyData['term_id']];
            }
        }

        return array_values(array_unique($relatedIds));
    }

    private function uniqueArticleSlug(string $sourceSlug, string $title, int $sourceId): string
    {
        return $this->uniqueSlug(
            $sourceSlug,
            $title,
            $sourceId,
            fn (string $slug): bool => Article::query()
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('wordpress_source_id')->orWhere('wordpress_source_id', '!=', $sourceId))
                ->exists()
        );
    }

    private function uniqueEventSlug(string $sourceSlug, string $title, int $sourceId): string
    {
        return $this->uniqueSlug(
            $sourceSlug,
            $title,
            $sourceId,
            fn (string $slug): bool => Event::query()
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('wordpress_source_id')->orWhere('wordpress_source_id', '!=', $sourceId))
                ->exists()
        );
    }

    private function uniqueSlug(string $sourceSlug, string $title, int $sourceId, \Closure $exists): string
    {
        $slug = Str::slug($sourceSlug ?: $title);
        $slug = $slug !== '' ? Str::limit($slug, 220, '') : 'wordpress-'.$sourceId;

        if (! $exists($slug)) {
            return $slug;
        }

        $suffix = '-wp-'.$sourceId;
        $base = Str::limit($slug, 220 - Str::length($suffix), '');
        $candidate = $base.$suffix;
        $duplicate = 2;

        while ($exists($candidate)) {
            $duplicateSuffix = $suffix.'-'.$duplicate++;
            $candidate = Str::limit($slug, 220 - Str::length($duplicateSuffix), '').$duplicateSuffix;
        }

        return $candidate;
    }

    private function parseUtcDate(?string $date): ?Carbon
    {
        if (blank($date) || str_starts_with($date, '0000-00-00')) {
            return null;
        }

        $parsed = Carbon::createFromFormat('!Y-m-d H:i:s', $date, 'UTC');

        return $parsed === false ? null : $parsed->setTimezone(config('app.timezone'));
    }

    private function parseEventDate(?string $date, string $timezone): ?Carbon
    {
        if (blank($date) || str_starts_with($date, '0000-00-00')) {
            return null;
        }

        $timezone = in_array($timezone, timezone_identifiers_list(), true) ? $timezone : 'UTC';
        $parsed = Carbon::createFromFormat('!Y-m-d H:i:s', $date, $timezone);

        return $parsed === false ? null : $parsed->setTimezone(config('app.timezone'));
    }

    private function plainText(string $content): string
    {
        $content = preg_replace('/<!--\s*\/?wp:.*?-->/s', '', $content) ?? $content;
        $content = preg_replace('/<(?:br\s*\/?|\/(?:p|div|li|h[1-6]|blockquote|section|article|tr))\b[^>]*>/i', "\n", $content) ?? $content;
        $content = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/[ \t]*\r?\n[ \t]*/', "\n", $content) ?? $content;
        $content = preg_replace('/\n{3,}/', "\n\n", $content) ?? $content;

        return trim($content);
    }

    private function attachmentUrl(?string $attachmentId): ?string
    {
        if (blank($attachmentId)) {
            return null;
        }

        return $this->safeUrl($this->posts[(int) $attachmentId]['guid'] ?? null);
    }

    private function safeUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            ? Str::limit($url, 255, '')
            : null;
    }
}
