<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin & Editor User
        $admin = User::firstOrCreate(
            ['email' => 'admin@infoscreening.id'],
            [
                'name' => 'Redaksi Infoscreening',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->update(['is_admin' => true]);

        // 2. Categories
        $categoryData = [
            ['name' => 'Artikel', 'color' => '#3A07BB', 'description' => 'Ulasan mendalam, esai, dan liputan seputar perfilman Indonesia dan dunia.'],
            ['name' => 'Berita', 'color' => '#E63946', 'description' => 'Kabar terkini seputar industri, kebijakan, dan dinamika perfilman.'],
            ['name' => 'Festival', 'color' => '#2A9D8F', 'description' => 'Seputar perhelatan festival film lokal dan internasional.'],
            ['name' => 'Pemutaran', 'color' => '#E76F51', 'description' => 'Agenda dan ulasan pemutaran film alternatif di berbagai ruang bioskop independen.'],
            ['name' => 'Wawancara', 'color' => '#8338EC', 'description' => 'Percakapan intim dengan sutradara, produser, kurator, dan kru film.'],
            ['name' => 'Kritik Film', 'color' => '#F4A261', 'description' => 'Refleksi kritis dan pembacaan estetika sinema.'],
        ];

        $categories = collect();
        foreach ($categoryData as $data) {
            $categories->push(Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data
            ));
        }

        // 3. Tags
        $tagNames = [
            'Film Indonesia', 'Sinema Rabu', 'Pemutaran Alternatif', 'Festival Film',
            'Minikino', 'Kineforum', 'Film Dokumenter', 'Yogyakarta', 'Film Pendek',
            'Jakarta', 'JAFF', 'Arkipel', 'Pesta Film Solo', 'Goethe Institut',
            'Film Musik Makan', 'Bandung', 'Surabaya', 'Solo', 'Bali', 'Arthouse Cinema',
        ];

        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => "Artikel dan agenda seputar {$name}"]
            ));
        }

        // 4. Articles
        $articlesData = [
            [
                'title' => 'Anggi Frisca: Perlunya Infrastruktur yang Menaungi Pekerja Film di Bawah HOD',
                'category' => 'Wawancara',
                'views' => 519,
                'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Sutradara dan sinematografer Anggi Frisca menyoroti pentingnya perlindungan ketenagakerjaan, standarisasi jam kerja, dan keberlanjutan regenerasi kru lini depan perfilman nasional.',
                'content' => "Dalam ekosistem perfilman yang terus melaju kencang, sorotan kerap tertuju pada sutradara, produser, atau jajaran pemeran utama. Namun di balik layar, terdapat puluhan hingga ratusan kru teknis—asisten kamera, gaffer, penata suara, hingga grip—yang bekerja dalam ritme ekstrem tanpa kepastian proteksi kerja yang ajeg.\n\nSinematografer dan sutradara Anggi Frisca menekankan perlunya sebuah infrastruktur yang secara konkret menaungi pekerja film di bawah Head of Department (HOD). \"Ketika kita bicara tentang industrialisasi perfilman, kita tidak hanya bisa merayakan lonjakan jumlah penonton di bioskop, tetapi juga harus memastikan kesejahteraan dan keamanan fisik maupun mental setiap awak yang menghidupkan produksi tersebut,\" ujarnya dalam sesi bincang santai bersama Infoscreening.\n\nMenurut Anggi, asosiasi profesi telah mulai memetakan pedoman kerja sehat, namun implementasi di lapangan masih sangat variatif bergantung pada produser masing-masing. Diperlukan kesepakatan kolektif dan komitmen lintas asosiasi agar jam kerja maksimal 14 jam bukan sekadar anjuran di atas kertas, melainkan standar industri yang ditegakkan dengan tegas.",
                'tags' => ['Film Indonesia', 'Festival Film', 'Jakarta'],
            ],
            [
                'title' => 'Tonny Trimarsanto: Pentingnya Perspektif Lokal di Ekosistem Film Daerah',
                'category' => 'Artikel',
                'views' => 609,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Sineas dokumenter Tonny Trimarsanto membagikan gagasannya tentang bagaimana narasi kearifan lokal memperkaya spektrum sinema Indonesia tanpa terjebak eksotisme.',
                'content' => "Sinema Indonesia selama bertahun-tahun didominasi oleh sudut pandang urban metropolitan. Namun menurut dokumentaris senior Tonny Trimarsanto, kekuatan sejati film dokumenter Indonesia terletak pada kemampuannya menyelami realitas manusia di berbagai pelosok tanah air secara jujur dan empatik.\n\n\"Perspektif lokal bukan sekadar menampilkan baju adat atau panorama alam, melainkan menangkap denyut nadi perjuangan hidup sehari-hari, cara bertutur, serta mitos dan kosmologi yang hidup di masyarakat,\" jelas Tonny dalam lokakarya penulisan dokumenter di Solo.\n\nMelalui Rumah Dokumenter yang digagasnya, Tonny konsisten mendampingi generasi muda di berbagai daerah di Jawa Tengah dan luar Jawa untuk menemukan suara mereka sendiri tanpa harus meniru kaidah visual komersial arus utama.",
                'tags' => ['Film Dokumenter', 'Solo', 'Yogyakarta'],
            ],
            [
                'title' => 'MSSE Hadirkan Diskusi dan Penayangan Film Women From Rote Island',
                'category' => 'Artikel',
                'views' => 831,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1478760329108-5c3ed9d495a0?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Program penayangan film peraih Film Terbaik FFI 2023 disusul diskusi hangat mengenai representasi kekerasan seksual dan keadilan adat di Pulau Rote.',
                'content' => "Komunitas sinema alternatif kembali menghadirkan ruang tonton kritis melalui screening film pemenang Piala Citra 'Women from Rote Island'. Film besutan sutradara Jeremias Nyangoen ini menjadi pemantik perbincangan mendalam tentang kekerasan berbasis gender dan tantangan sistem hukum adat di wilayah timur Indonesia.\n\nPenayangan dihadiri oleh ratusan penonton yang memadati aula pemutaran. Diskusi pasca-tayang mengurai bagaimana medium sinema dapat menjadi alat advokasi yang kuat sekaligus sarana refleksi nurani bangsa.",
                'tags' => ['Pemutaran Alternatif', 'Film Indonesia', 'Kineforum'],
            ],
            [
                'title' => 'Melihat Film Pendek Jadi yang Utama dalam Minikino ke-11',
                'category' => 'Artikel',
                'views' => 777,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Minikino Film Week terus membuktikan bahwa film pendek bukan batu loncatan menuju film panjang, melainkan bentuk seni otonom dengan daya ledak tak terbatas.',
                'content' => 'Perhelatan tahunan Minikino Film Week ke-11 di Bali menyajikan ratusan film pendek dari puluhan negara. Lebih dari sekadar ajang tayang, Minikino menghadirkan pasar film pendek (Short Film Market), forum pitching, dan klinik kurasi internasional yang membuka peluang jejaring global bagi sineas muda Indonesia.',
                'tags' => ['Minikino', 'Film Pendek', 'Bali'],
            ],
            [
                'title' => 'Dukung Pengembangan Talenta Baru, Jakarta Film Week 2026 Kembali Membuka JFWNET-Industry Program',
                'category' => 'Event',
                'views' => 156,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Jakarta Film Week membuka submisi proyek film untuk program JFWNET: Pitching Forum dan Series of Masterclasses.',
                'content' => 'Festival film internasional Jakarta Film Week mengumumkan pembukaan pendaftaran program industri JFWNET 2026. Program ini dirancang untuk mempertemukan pembuat film bertalenta dengan produser, distributor, festival programmer, dan investor dari kawasan Asia Tenggara dan dunia.',
                'tags' => ['Festival Film', 'Jakarta', 'Arthouse Cinema'],
            ],
            [
                'title' => 'Picnic Cinema Lokananta: Menengok Ulang Luka Bangsa Melalui Film Eksil',
                'category' => 'Pemutaran',
                'views' => 100,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1518676590629-3dcbd9c5a5c9?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Film pemenang Piala Citra FFI 2023, Eksil, diputar di Taman Lingkar Lokananta, Surakarta. Menghadirkan suasana reflektif di ruang bersejarah musik nusantara.',
                'content' => "Di bawah rimbun pohon dan gemerlap lampu malam di kompleks cagar budaya Lokananta, ratusan penonton menyaksikan film 'Eksil' karya Lola Amaria. Dokumenter yang merekam nasib para mahasiswa dan intelektual Indonesia yang kehilangan kewarganegaraan pasca-peristiwa 1965 di Eropa ini menyentuh hati para generasi muda yang hadir.",
                'tags' => ['Pemutaran Alternatif', 'Solo', 'Film Dokumenter'],
            ],
            [
                'title' => 'Tiga Film dengan Perspektif Anak Raih Penghargaan di NextScene Film Festival 2026',
                'category' => 'Festival',
                'views' => 229,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1460723237483-7a6dc9d0b212?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'NextScene Kids & Youth Film Festival 2026 resmi menutup perhelatannya di Bioskop Kineforum, Jakarta.',
                'content' => 'NextScene Film Festival 2026 membuktikan pentingnya sinema anak yang menempatkan anak sebagai subjek cerita, bukan sekadar objek hiburan dangkal. Tiga karya terbaik berhasil memukau dewan juri cilik dan juri profesional.',
                'tags' => ['Festival Film', 'Kineforum', 'Jakarta'],
            ],
            [
                'title' => 'Dua Puluh Film Jepang Tayang Daring di Japanese Film Festival Online',
                'category' => 'Event',
                'views' => 142500,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Akses gratis bagi pecinta sinema Jepang di Indonesia untuk menikmati 20 film pilihan dari klasik hingga animasi modern.',
                'content' => 'The Japan Foundation menyelenggarakan Japanese Film Festival (JFF) Online yang dapat diakses secara cuma-cuma oleh pemirsa di Indonesia dengan teks terjemahan bahasa Indonesia dan Inggris. Antusiasme penonton mencatatkan rekor tertinggi dalam sejarah festival daring.',
                'tags' => ['Festival Film', 'Pemutaran Alternatif'],
            ],
            [
                'title' => 'Minikino Film Week 9, Segera Hadir Pada 15-23 September',
                'category' => 'Artikel',
                'views' => 19500,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1524712245354-2c4e5e7121c0?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Mengusung tema jejaring mikro yang mandiri, MFW 9 menyiapkan lebih dari 60 program penayangan keliling di desa-desa di Bali.',
                'content' => "Minikino Film Week ke-9 menghadirkan sinema mikro ke tengah masyarakat melalui program 'Micro Cinema' dan 'Pop-Up Cinema' di balai banjar, kafe, dan ruang terbuka di seluruh penjuru Bali.",
                'tags' => ['Minikino', 'Bali', 'Film Pendek'],
            ],
            [
                'title' => 'Dokumenter Musik BTS dan Coldplay Rilis November di Indonesia',
                'category' => 'Artikel',
                'views' => 16800,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Eksplorasi proses kreatif kolaborasi lintas benua dalam format sinematik yang memukau layar lebar.',
                'content' => "Film dokumenter musik yang merekam perjalanan kolaborasi BTS dan Coldplay dalam produksi lagu hit global 'My Universe' akhirnya menyapa bioskop-bioskop di tanah air.",
                'tags' => ['Film Musik Makan', 'Film Indonesia'],
            ],
            [
                'title' => 'Batalnya Bulan Film Nasional Kineforum dan Tata Kelola Taman Ismail Marzuki',
                'category' => 'Berita',
                'views' => 15600,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Kritik tajam komunitas film terhadap komersialisasi berlebihan ruang budaya di TIM yang meminggirkan inisiatif pemutaran nirlaba.',
                'content' => 'Kekecewaan melanda pegiat sinema alternatif di ibu kota setelah perayaan Bulan Film Nasional di Bioskop Kineforum terhambat persoalan tata kelola dan tarif sewa ruang di Taman Ismail Marzuki.',
                'tags' => ['Kineforum', 'Jakarta', 'Sinema Rabu'],
            ],
            [
                'title' => 'Laut Bercerita (The Sea Speaks His Name): Bangkitkan Kenangan melalui Tragedi 1998',
                'category' => 'Pemutaran',
                'views' => 15500,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Film pendek karya Yosep Anggi Noen yang diadaptasi dari novel Leila S. Chudori terus diputar di berbagai kampus dan komunitas di Indonesia.',
                'content' => "Hingga kini, 'Laut Bercerita' menjadi film pendek yang paling sering diputar dalam acara-acara diskusi mahasiswa dan aktivis hak asasi manusia di penjuru negeri.",
                'tags' => ['Pemutaran Alternatif', 'Film Pendek', 'Yogyakarta'],
            ],
            [
                'title' => 'Sekolah Film di Yogyakarta: Dari Biaya Sampai yang Dipelajari',
                'category' => 'Artikel',
                'views' => 14400,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Panduan lengkap bagi calon mahasiswa dan sineas muda yang ingin menimba ilmu sinema di kota gudang sineas independen.',
                'content' => 'Yogyakarta dikenal sebagai kawah candradimuka perfilman independen Indonesia. Mulai dari ISI Yogyakarta, MMTC, hingga akademi-akademi film swasta dan komunitas belajar mandiri seperti Klub DIY Menonton.',
                'tags' => ['Yogyakarta', 'Film Indonesia'],
            ],
            [
                'title' => 'Film Maha Guru Tan Malaka: Dari Cita-cita, ke Film Pendek, Menuju Film Panjang',
                'category' => 'Artikel',
                'views' => 13500,
                'is_featured' => false,
                'image' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&w=1200&q=80',
                'excerpt' => 'Catatan perjalanan produksi film independen yang mengangkat tokoh pemikir revolusioner Tan Malaka di tengah keterbatasan modal.',
                'content' => 'Membuat film tentang Tan Malaka membutuhkan keberanian riset sejarah dan komitmen produksi independen yang gigih. Simak wawancara mendalam tim produksi bersama Infoscreening.',
                'tags' => ['Film Dokumenter', 'Film Indonesia'],
            ],
        ];

        foreach ($articlesData as $art) {
            $cat = $categories->firstWhere('name', $art['category']) ?? $categories->first();
            $article = Article::updateOrCreate(
                ['slug' => Str::slug($art['title'])],
                [
                    'user_id' => $admin->id,
                    'title' => $art['title'],
                    'excerpt' => $art['excerpt'],
                    'content' => $art['content'],
                    'featured_image' => $art['image'],
                    'status' => 'published',
                    'is_featured' => $art['is_featured'],
                    'published_at' => now()->subDays(rand(1, 45))->subHours(rand(1, 23)),
                    'meta_title' => $art['title'].' - Infoscreening',
                    'meta_description' => Str::limit($art['excerpt'], 155),
                    'reading_time' => rand(3, 7),
                    'views' => $art['views'],
                ]
            );

            $article->categories()->sync([$cat->id]);

            $matchedTags = $tags->whereIn('name', $art['tags'])->pluck('id');
            if ($matchedTags->isNotEmpty()) {
                $article->tags()->sync($matchedTags);
            }
        }

        // 5. Events / Kalender
        $eventsData = [
            [
                'title' => 'Jakarta Film Week 2026: JFWNET Industry Program',
                'type' => 'Festival',
                'venue' => 'CGV Grand Indonesia & Kineforum',
                'city' => 'Jakarta',
                'starts_at' => now()->addDays(12)->setTime(10, 0),
                'ends_at' => now()->addDays(16)->setTime(22, 0),
                'description' => 'Festival film internasional berskala besar dengan pemutaran kompetisi, pitching forum, masterclass, dan jejaring industri film se-Asia Tenggara.',
                'image' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=800&q=80',
                'url' => 'https://jakartafilmweek.com',
            ],
            [
                'title' => 'Minikino Film Week 11 (MFW11) International Short Film Festival',
                'type' => 'Festival',
                'venue' => 'MASH Denpasar & Banjar Network',
                'city' => 'Bali',
                'starts_at' => now()->addDays(20)->setTime(14, 0),
                'ends_at' => now()->addDays(28)->setTime(23, 0),
                'description' => 'Festival film pendek internasional tahunan di Bali yang menghadirkan ratusan film pendek terkurasi, workshop, and short film market.',
                'image' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80',
                'url' => 'https://minikino.org',
            ],
            [
                'title' => 'Sinema Rabu: Pemutaran Film Dokumenter Arsip Budaya',
                'type' => 'Pemutaran',
                'venue' => 'Galeri Indonesia Kaya, Grand Indonesia',
                'city' => 'Jakarta',
                'starts_at' => now()->addDays(4)->setTime(19, 0),
                'ends_at' => now()->addDays(4)->setTime(21, 30),
                'description' => 'Program rutin pemutaran film alternatif dan diskusi hangat bersama kurator dan pengkaji sinema independen.',
                'image' => 'https://images.unsplash.com/photo-1478760329108-5c3ed9d495a0?auto=format&fit=crop&w=800&q=80',
                'url' => 'https://infoscreening.id',
            ],
            [
                'title' => 'Jogja-NETPAC Asian Film Festival (JAFF 2026)',
                'type' => 'Festival',
                'venue' => 'Empire XXI & Taman Budaya Yogyakarta',
                'city' => 'Yogyakarta',
                'starts_at' => now()->addDays(45)->setTime(9, 0),
                'ends_at' => now()->addDays(52)->setTime(23, 0),
                'description' => 'Perayaan sinema Asia terbesar di Indonesia yang mempertemukan sineas, kritikus, dan ribuan penonton dari seluruh penjuru negeri.',
                'image' => 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&w=800&q=80',
                'url' => 'https://jaff-filmfest.org',
            ],
            [
                'title' => 'Lokakarya Kurasi dan Manajemen Pemutaran Komunitas',
                'type' => 'Workshop',
                'venue' => 'Bioskop Misbar Usman Janatin',
                'city' => 'Purbalingga',
                'starts_at' => now()->addDays(8)->setTime(13, 0),
                'ends_at' => now()->addDays(9)->setTime(17, 0),
                'description' => 'Pelatihan intensif bagi pengelola bioskop alternatif, festival kampus, dan klub film daerah untuk menyusun program sinema bermutu.',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80',
                'url' => 'https://infoscreening.id',
            ],
        ];

        foreach ($eventsData as $ev) {
            Event::updateOrCreate(
                ['slug' => Str::slug($ev['title'])],
                array_merge($ev, ['is_published' => true])
            );
        }

        // 6. Experts (Tenaga Ahli) for Rupa Jasa
        $expertsData = [
            [
                'name' => 'Budi Laksono, M.Sn',
                'specialty' => 'Kurator Film & Konsultan Pemrograman Festival',
                'city' => 'Jakarta',
                'bio' => 'Berpengalaman lebih dari 12 tahun mengelola program kurasi festival film independen di Indonesia dan Asia Tenggara. Siap mendampingi perancangan program sinema berkualitas.',
                'email' => 'budi.laksono@infoscreening.id',
                'phone' => '+62 812-3456-7890',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'is_published' => true,
            ],
            [
                'name' => 'Rara Sekar Kinanti',
                'specialty' => 'Pengarsip Sinema & Peneliti Sejarah Film',
                'city' => 'Yogyakarta',
                'bio' => 'Spesialis dalam preservasi film seluloid, digitalisasi arsip audio visual, dan penulisan monograf sejarah sinema lokal Indonesia.',
                'email' => 'rara.sekar@infoscreening.id',
                'phone' => '+62 813-9876-5432',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'is_published' => true,
            ],
            [
                'name' => 'Dhimas Prasetyo',
                'specialty' => 'Manajer Distribusi Film Pendek & Alternatif',
                'city' => 'Bandung',
                'bio' => 'Membantu sineas mandiri menjangkau sirkuit festival internasional, pasar film, dan platform streaming berbasis kurasi.',
                'email' => 'dhimas@infoscreening.id',
                'phone' => '+62 811-2233-4455',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                'is_published' => true,
            ],
            [
                'name' => 'Citra Ayu Lestari',
                'specialty' => 'Teknisi Proyeksi & Akustik Ruang Pemutaran',
                'city' => 'Surabaya',
                'bio' => 'Ahli tata audio-visual ruang pemutaran, kalibrasi DCI-projector, sound system mikro-sinema, dan manajemen teknis acara screening.',
                'email' => 'citra.ayu@infoscreening.id',
                'phone' => '+62 815-6677-8899',
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'is_published' => true,
            ],
        ];

        foreach ($expertsData as $exp) {
            Expert::updateOrCreate(
                ['name' => $exp['name']],
                $exp
            );
        }

        // 7. Products (Toko / Merch)
        $productsData = [
            [
                'name' => 'Zine Infoscreening Vol. 4: Sinema Alternatif & Lanskap Komunitas',
                'description' => 'Edisi cetak khusus berisi kumpulan esai kritis, arsip foto pemutaran mandiri dari 10 kota, dan direktori klub film Indonesia.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                'buy_url' => 'https://tokopedia.com',
                'is_available' => true,
            ],
            [
                'name' => 'T-Shirt Infoscreening Exclusive "Arthouse Cinema Enthusiast"',
                'description' => 'Kaos katun combed 24s dengan sablon plastisol premium warna ungu ciri khas #3A07BB Infoscreening. Nyaman untuk nonton maraton di bioskop.',
                'price' => 150000,
                'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
                'buy_url' => 'https://tokopedia.com',
                'is_available' => true,
            ],
            [
                'name' => 'Tote Bag Kanvas "Film Adalah Perjalanan"',
                'description' => 'Tote bag kanvas tebal dengan ritsleting dan saku dalam, muat laptop 14 inci serta buku catatan festivalmu.',
                'price' => 75000,
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
                'buy_url' => 'https://tokopedia.com',
                'is_available' => true,
            ],
            [
                'name' => 'Enamel Pin Infoscreening Classic 35mm Reel',
                'description' => 'Pin logam enamel berkualitas tinggi bertema pita seluloid 35mm untuk menghias jaket atau tas pemutaranmu.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
                'buy_url' => 'https://tokopedia.com',
                'is_available' => true,
            ],
        ];

        foreach ($productsData as $prod) {
            Product::updateOrCreate(
                ['slug' => Str::slug($prod['name'])],
                $prod
            );
        }

        // 8. Sample Contributions
        Contribution::updateOrCreate(
            ['email' => 'sineas.muda@gmail.com', 'title' => 'Liputan Pemutaran Layar Tanjab di Cirebon'],
            [
                'name' => 'Rian Ardiansyah',
                'type' => 'Liputan',
                'message' => 'Halo redaksi Infoscreening, kami dari komunitas Film Pantura baru saja mengadakan pemutaran layar tanjab di 3 desa pesisir. Kami ingin mengirimkan naskah liputan dan fotonya.',
                'link' => 'https://drive.google.com',
                'status' => 'pending',
            ]
        );

        Contribution::updateOrCreate(
            ['email' => 'klub.sinema@yahoo.com', 'title' => 'Pendaftaran Agenda Festival Film Kampus Bandung'],
            [
                'name' => 'Siti Nurhaliza',
                'type' => 'Agenda / Event',
                'message' => 'Mohon bantuan untuk mencantumkan festival tahunan kami ke dalam Kalender Event Infoscreening bulan depan. Terima kasih.',
                'link' => 'https://instagram.com/ffk_bandung',
                'status' => 'approved',
            ]
        );

        // 9. Initial Strapi Media Library Assets
        foreach (Article::whereNotNull('featured_image')->get() as $art) {
            Media::firstOrCreate(
                ['file_path' => $art->featured_image],
                [
                    'name' => basename(parse_url($art->featured_image, PHP_URL_PATH) ?? 'article.jpg') ?: 'article.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(250000, 680000),
                    'width' => 1920,
                    'height' => 1080,
                    'caption' => $art->title,
                ]
            );
        }

        foreach (Event::whereNotNull('image')->get() as $ev) {
            Media::firstOrCreate(
                ['file_path' => $ev->image],
                [
                    'name' => basename(parse_url($ev->image, PHP_URL_PATH) ?? 'event.jpg') ?: 'event.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(180000, 520000),
                    'width' => 1200,
                    'height' => 800,
                    'caption' => $ev->title,
                ]
            );
        }

        foreach (Product::whereNotNull('image')->get() as $pr) {
            Media::firstOrCreate(
                ['file_path' => $pr->image],
                [
                    'name' => basename(parse_url($pr->image, PHP_URL_PATH) ?? 'product.jpg') ?: 'product.jpg',
                    'mime_type' => 'image/jpeg',
                    'size' => rand(150000, 410000),
                    'width' => 800,
                    'height' => 800,
                    'caption' => $pr->name,
                ]
            );
        }
    }
}
