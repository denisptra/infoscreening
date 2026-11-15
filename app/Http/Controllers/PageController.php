<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $pages = [
            'tentang-kami' => [
                'title' => 'Tentang Kami',
                'content' => [
                    'Dimulai dari kegemaran menonton film-film pada festival film dan pemutaran alternatif, Infoscreening merupakan usaha memberikan informasi seputar pemutaran alternatif dan juga pemutaran film pada festival-festival film yang ada di Indonesia.',
                    'Usaha ini dimulai dari akun Twitter kami yang telah dibuat sejak tahun 2012, @infoscreening, berlanjut dengan situs web Infoscreening. Diharapkan usaha ini dapat berguna bagi semua aspek semesta perfilman di tanah air.',
                    'Kami meliput festival film, pemutaran alternatif, wawancara dengan pelaku film, hingga kalender acara sinema dari berbagai kota di Indonesia.',
                ],
            ],
            'kontak' => [
                'title' => 'Kontak',
                'content' => [
                    'Untuk kerja sama, liputan, atau pertanyaan redaksi, silakan hubungi tim Infoscreening melalui surel kontak@infoscreening.id atau lewat media sosial kami di @infoscreening.',
                ],
            ],
            'kebijakan-privasi' => [
                'title' => 'Kebijakan Privasi',
                'content' => [
                    'Informasi pribadi yang dikumpulkan melalui situs ini hanya digunakan untuk kebutuhan redaksi, komunikasi, dan pengembangan layanan. Kami tidak menjual data pengguna.',
                ],
            ],
        ];

        abort_unless(array_key_exists($slug, $pages), 404);

        return view('pages.show', ['page' => $pages[$slug]]);
    }
}
