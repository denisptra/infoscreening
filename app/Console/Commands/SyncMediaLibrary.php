<?php

namespace App\Console\Commands;

use App\Support\MediaLibrarySync;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sync-media-library {--wp-dump= : Path opsional ke file SQL WordPress untuk ikut mengindeks lampiran gambar}')]
#[Description('Satukan gambar dari artikel, event, direktori, storage publik, dan backup WordPress ke Media Library')]
class SyncMediaLibrary extends Command
{
    public function handle(MediaLibrarySync $sync): int
    {
        $wpDump = $this->option('wp-dump');

        if (filled($wpDump) && ! is_file((string) $wpDump)) {
            $this->components->error("File dump WordPress tidak ditemukan: {$wpDump}");

            return self::FAILURE;
        }

        $stats = $sync->sync(filled($wpDump) ? (string) $wpDump : null);

        $this->components->info(sprintf(
            'Sinkronisasi Media Library selesai: %s aset baru ditambahkan (%s dari konten DB, %s dari storage publik, %s dari dump WordPress, %s dilewati).',
            number_format($stats['added']),
            number_format($stats['from_content']),
            number_format($stats['from_storage']),
            number_format($stats['from_wp_dump']),
            number_format($stats['skipped'])
        ));

        return self::SUCCESS;
    }
}
