<?php

namespace Tests\Feature;

use App\Console\Commands\ImportWordPressContent;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportWordPressContentTest extends TestCase
{
    public function test_import_refuses_to_run_when_database_is_not_mysql(): void
    {
        $exitCode = Artisan::call('app:import-wordpress-content', [
            'dump' => 'wordpress.sql',
        ]);

        $this->assertSame(ImportWordPressContent::FAILURE, $exitCode);
        $this->assertStringContainsString('belum menggunakan MySQL', Artisan::output());
    }
}
