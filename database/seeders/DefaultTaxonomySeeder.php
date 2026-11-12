<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\NavigationItem;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class DefaultTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categoryDefinitions = [
            [
                'name' => 'Berita Film',
                'slug' => 'berita-film',
                'color' => '#ba233b',
                'legacy_labels' => ['Berita Perfilman'],
                'legacy_urls' => ['/kategori/berita'],
            ],
            [
                'name' => 'Festival Film',
                'slug' => 'festival-film',
                'color' => '#7344ff',
                'legacy_labels' => [],
                'legacy_urls' => ['/kategori/festival'],
            ],
            [
                'name' => 'Wawancara Sineas',
                'slug' => 'wawancara-sineas',
                'color' => '#16807a',
                'legacy_labels' => [],
                'legacy_urls' => ['/kategori/wawancara'],
            ],
            [
                'name' => 'Kritik Film',
                'slug' => 'kritik-film',
                'color' => '#b55a2a',
                'legacy_labels' => [],
                'legacy_urls' => [],
            ],
            [
                'name' => 'Pemutaran Film',
                'slug' => 'pemutaran-film',
                'color' => '#2563eb',
                'legacy_labels' => [],
                'legacy_urls' => [],
            ],
        ];

        $headerMenu = NavigationItem::query()->firstOrCreate(
            ['location' => 'header', 'parent_id' => null, 'url' => '/artikel'],
            ['label' => 'Artikel', 'sort_order' => 20, 'is_active' => true]
        );
        $footerMenu = NavigationItem::query()->firstOrCreate(
            ['location' => 'footer', 'parent_id' => null, 'label' => 'Rubrikasi Artikel'],
            ['url' => null, 'sort_order' => 10, 'is_active' => true]
        );

        foreach ($categoryDefinitions as $index => $definition) {
            $category = Category::query()
                ->where('slug', $definition['slug'])
                ->orWhere('name', $definition['name'])
                ->first();

            if (! $category) {
                $category = Category::query()->create([
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                    'color' => $definition['color'],
                ]);
            }

            $legacyLabels = array_merge([$definition['name']], $definition['legacy_labels']);
            $sortOrder = ($index + 1) * 10;

            $this->syncCategoryMenuItem($headerMenu, $category, $legacyLabels, $definition['legacy_urls'], $sortOrder);
            $this->syncCategoryMenuItem($footerMenu, $category, $legacyLabels, $definition['legacy_urls'], $sortOrder);
        }

        foreach ([
            ['name' => 'Indonesia', 'slug' => 'indonesia'],
            ['name' => 'Festival Film', 'slug' => 'festival-film'],
            ['name' => 'Sinema Baru', 'slug' => 'sinema-baru'],
            ['name' => 'Dokumenter', 'slug' => 'dokumenter'],
            ['name' => 'Kritik Film', 'slug' => 'kritik-film'],
            ['name' => 'Masyarakat', 'slug' => 'masyarakat'],
            ['name' => 'Kebudayaan', 'slug' => 'kebudayaan'],
            ['name' => 'Film Pendek', 'slug' => 'film-pendek'],
        ] as $definition) {
            $tagExists = Tag::query()
                ->where('slug', $definition['slug'])
                ->orWhere('name', $definition['name'])
                ->exists();

            if (! $tagExists) {
                Tag::query()->create($definition);
            }
        }
    }

    /**
     * @param  array<int, string>  $knownLabels
     * @param  array<int, string>  $legacyUrls
     */
    private function syncCategoryMenuItem(
        NavigationItem $parent,
        Category $category,
        array $knownLabels,
        array $legacyUrls,
        int $sortOrder
    ): void {
        $categoryUrl = '/kategori/'.$category->slug;
        $item = $parent->children()
            ->where(function (Builder $query) use ($categoryUrl, $knownLabels, $legacyUrls): void {
                $query->whereIn('label', $knownLabels)
                    ->orWhereIn('url', array_merge([$categoryUrl], $legacyUrls));
            })
            ->first();

        if (! $item) {
            $parent->children()->create([
                'location' => $parent->location,
                'label' => $category->name,
                'url' => $categoryUrl,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);

            return;
        }

        $item->url = $categoryUrl;

        if (in_array($item->label, $knownLabels, true)) {
            $item->label = $category->name;
        }

        $item->save();
    }
}
