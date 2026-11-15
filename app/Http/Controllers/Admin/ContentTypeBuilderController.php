<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class ContentTypeBuilderController extends Controller
{
    public function index(): View
    {
        $collectionTypes = [
            [
                'name' => 'Article',
                'api_id' => 'article',
                'plural' => 'articles',
                'description' => 'Berita, ulasan film, liputan khusus sinema alternatif dan artikel kurasi.',
                'model' => 'App\Models\Article',
                'count' => Article::count(),
                'fields' => [
                    ['name' => 'title', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'excerpt', 'type' => 'Text', 'required' => false, 'unique' => false],
                    ['name' => 'content', 'type' => 'Rich Text (HTML)', 'required' => true, 'unique' => false],
                    ['name' => 'featured_image', 'type' => 'Media (Single)', 'required' => false, 'unique' => false],
                    ['name' => 'status', 'type' => 'Enumeration (draft, published)', 'required' => true, 'unique' => false],
                    ['name' => 'views', 'type' => 'BigInteger', 'required' => false, 'unique' => false],
                    ['name' => 'published_at', 'type' => 'DateTime', 'required' => false, 'unique' => false],
                    ['name' => 'primary_category_id', 'type' => 'Relation (HasOne Category)', 'required' => false, 'unique' => false],
                    ['name' => 'categories', 'type' => 'Relation (BelongsToMany Category)', 'required' => false, 'unique' => false],
                    ['name' => 'tags', 'type' => 'Relation (BelongsToMany Tag)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Category',
                'api_id' => 'category',
                'plural' => 'categories',
                'description' => 'Taksonomi rubrik kanal (Berita, Ulasan, Rupa Jasa, dsb).',
                'model' => 'App\Models\Category',
                'count' => Category::count(),
                'fields' => [
                    ['name' => 'name', 'type' => 'String', 'required' => true, 'unique' => true],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'description', 'type' => 'Text', 'required' => false, 'unique' => false],
                    ['name' => 'color', 'type' => 'String (HEX)', 'required' => false, 'unique' => false],
                    ['name' => 'articles', 'type' => 'Relation (HasMany Article)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Tag',
                'api_id' => 'tag',
                'plural' => 'tags',
                'description' => 'Label kata kunci penanda topik sinema dan sineas.',
                'model' => 'App\Models\Tag',
                'count' => Tag::count(),
                'fields' => [
                    ['name' => 'name', 'type' => 'String', 'required' => true, 'unique' => true],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'articles', 'type' => 'Relation (BelongsToMany Article)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Event',
                'api_id' => 'event',
                'plural' => 'events',
                'description' => 'Kalender agenda pemutaran, festival, workshop, dan submit film.',
                'model' => 'App\Models\Event',
                'count' => Event::count(),
                'fields' => [
                    ['name' => 'title', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'venue', 'type' => 'String', 'required' => false, 'unique' => false],
                    ['name' => 'city', 'type' => 'String', 'required' => false, 'unique' => false],
                    ['name' => 'starts_at', 'type' => 'Date', 'required' => true, 'unique' => false],
                    ['name' => 'ends_at', 'type' => 'Date', 'required' => false, 'unique' => false],
                    ['name' => 'registration_url', 'type' => 'String (URL)', 'required' => false, 'unique' => false],
                    ['name' => 'ticket_price', 'type' => 'String', 'required' => false, 'unique' => false],
                    ['name' => 'banner_image', 'type' => 'Media (Single)', 'required' => false, 'unique' => false],
                    ['name' => 'description', 'type' => 'Rich Text (HTML)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Expert',
                'api_id' => 'expert',
                'plural' => 'experts',
                'description' => 'Direktori kurator, sutradara, produser, dan tenaga ahli sinema.',
                'model' => 'App\Models\Expert',
                'count' => Expert::count(),
                'fields' => [
                    ['name' => 'name', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'role', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'bio', 'type' => 'Text', 'required' => false, 'unique' => false],
                    ['name' => 'city', 'type' => 'String', 'required' => false, 'unique' => false],
                    ['name' => 'email', 'type' => 'Email', 'required' => false, 'unique' => false],
                    ['name' => 'phone', 'type' => 'String', 'required' => false, 'unique' => false],
                    ['name' => 'photo_url', 'type' => 'Media (Single)', 'required' => false, 'unique' => false],
                    ['name' => 'portfolio_url', 'type' => 'String (URL)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Product',
                'api_id' => 'product',
                'plural' => 'products',
                'description' => 'Katalog merchandise, buku sinema, tiket, dan rilisan fisik.',
                'model' => 'App\Models\Product',
                'count' => Product::count(),
                'fields' => [
                    ['name' => 'name', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'slug', 'type' => 'UID', 'required' => true, 'unique' => true],
                    ['name' => 'price', 'type' => 'Decimal / Currency', 'required' => true, 'unique' => false],
                    ['name' => 'description', 'type' => 'Text', 'required' => false, 'unique' => false],
                    ['name' => 'image_url', 'type' => 'Media (Single)', 'required' => false, 'unique' => false],
                    ['name' => 'purchase_url', 'type' => 'String (URL)', 'required' => false, 'unique' => false],
                ],
            ],
            [
                'name' => 'Media',
                'api_id' => 'upload_file',
                'plural' => 'media',
                'description' => 'Strapi Media Library assets, foto banner, poster, dan dokumen.',
                'model' => 'App\Models\Media',
                'count' => Media::count(),
                'fields' => [
                    ['name' => 'name', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'file_name', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'mime_type', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'disk', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'path', 'type' => 'String', 'required' => true, 'unique' => false],
                    ['name' => 'size', 'type' => 'BigInteger (bytes)', 'required' => true, 'unique' => false],
                    ['name' => 'width', 'type' => 'Integer', 'required' => false, 'unique' => false],
                    ['name' => 'height', 'type' => 'Integer', 'required' => false, 'unique' => false],
                ],
            ],
        ];

        return view('admin.content-types.index', [
            'collectionTypes' => $collectionTypes,
            'selected' => request('type', 'article'),
        ]);
    }
}
