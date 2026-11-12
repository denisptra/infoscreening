<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->string('location', 20);
            $table->string('label', 100);
            $table->string('url', 2048)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['location', 'parent_id', 'is_active', 'sort_order']);
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });

        $now = now();
        $navigationItems = [
            [1, null, 'header', 'Beranda', '/', 10, false],
            [2, null, 'header', 'Artikel', '/artikel', 20, false],
            [3, null, 'header', 'Kalender Event', '/events', 30, false],
            [4, null, 'header', 'Berkontribusi', '/berkontribusi', 40, false],
            [5, null, 'header', 'Tentang Kami', '/tentang-kami', 50, false],
            [6, null, 'header', 'Rupa Jasa', '/rupa-jasa', 60, false],
            [7, null, 'header', 'Toko / Zine', '/toko', 70, false],
            [8, 2, 'header', 'Berita Film', '/kategori/berita', 10, false],
            [9, 2, 'header', 'Wawancara Sineas', '/kategori/wawancara', 20, false],
            [10, 2, 'header', 'Kritik Film', '/kategori/kritik-film', 30, false],
            [11, 5, 'header', 'Profil Infoscreening', '/tentang-kami', 10, false],
            [12, 5, 'header', 'Daftar Penulis', '/authors-list', 20, false],
            [13, 5, 'header', 'Kontak Redaksi', '/kontak', 30, false],
            [14, 5, 'header', 'Kebijakan Privasi', '/kebijakan-privasi', 40, false],
            [15, 6, 'header', 'Layanan Kemitraan', '/rupa-jasa', 10, false],
            [16, 6, 'header', 'Tenaga Ahli Sinema', '/rupa-jasa/tenaga-ahli', 20, false],
            [17, null, 'footer', 'Rubrikasi Artikel', null, 10, false],
            [18, null, 'footer', 'Ekosistem Sinema', null, 20, false],
            [19, null, 'footer', 'Ikuti Kami', null, 30, false],
            [20, 17, 'footer', 'Semua Artikel', '/artikel', 10, false],
            [21, 17, 'footer', 'Berita Perfilman', '/kategori/berita', 20, false],
            [22, 17, 'footer', 'Festival Film', '/kategori/festival', 30, false],
            [23, 18, 'footer', 'Kalender Acara', '/events', 10, false],
            [24, 18, 'footer', 'Kirim Tulisan', '/berkontribusi', 20, false],
            [25, 18, 'footer', 'Tenaga Ahli Film', '/rupa-jasa/tenaga-ahli', 30, false],
            [26, 18, 'footer', 'Kontak Redaksi', '/kontak', 40, false],
            [27, 19, 'footer', 'Instagram', 'https://instagram.com/infoscreening', 10, true],
            [28, 19, 'footer', 'YouTube', 'https://youtube.com/user/infoscreening', 20, true],
        ];

        DB::table('navigation_items')->insert(array_map(
            fn (array $item): array => [
                'id' => $item[0],
                'parent_id' => $item[1],
                'location' => $item[2],
                'label' => $item[3],
                'url' => $item[4],
                'sort_order' => $item[5],
                'open_in_new_tab' => $item[6],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $navigationItems
        ));

        DB::table('site_settings')->insert([
            [
                'key' => 'footer_about',
                'value' => 'Media informasi pemutaran sinema alternatif, festival film, dan ekosistem perfilman Indonesia.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'footer_copyright',
                'value' => 'Infoscreening.id',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('navigation_items');
    }
};
