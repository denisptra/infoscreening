<?php

namespace Tests\Unit\Support;

use App\Support\WordPressSqlDumpReader;
use RuntimeException;
use Tests\TestCase;

class WordPressSqlDumpReaderTest extends TestCase
{
    public function test_reads_only_allowlisted_tables_and_decodes_sql_values(): void
    {
        $dumpPath = tempnam(sys_get_temp_dir(), 'wordpress-dump-');
        $dump = <<<'SQL'
CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL
);
INSERT INTO `wp_posts` (`ID`, `post_title`, `post_content`) VALUES
(1, 'Judul, artikel', 'Baris pertama\nBaris kedua'),
(2, 'Judul ''kutipan''', 'Isi artikel');
INSERT INTO `wp_users` (`ID`, `display_name`) VALUES
(10, 'Nama Penulis');
SQL;

        file_put_contents($dumpPath, $dump);

        try {
            $reader = new WordPressSqlDumpReader;
            $rows = iterator_to_array($reader->rows($dumpPath, 'wp_', ['posts']));

            $this->assertSame('wp_', $reader->detectPrefix($dumpPath));
            $this->assertSame([
                [
                    'table' => 'posts',
                    'fields' => [
                        'ID' => '1',
                        'post_title' => 'Judul, artikel',
                        'post_content' => "Baris pertama\nBaris kedua",
                    ],
                ],
                [
                    'table' => 'posts',
                    'fields' => [
                        'ID' => '2',
                        'post_title' => "Judul 'kutipan'",
                        'post_content' => 'Isi artikel',
                    ],
                ],
            ], $rows);
        } finally {
            unlink($dumpPath);
        }
    }

    public function test_rejects_an_incomplete_insert_statement(): void
    {
        $dumpPath = tempnam(sys_get_temp_dir(), 'wordpress-dump-');
        file_put_contents($dumpPath, "INSERT INTO `wp_posts` (`ID`) VALUES\n(1,");

        try {
            $this->expectException(RuntimeException::class);
            iterator_to_array((new WordPressSqlDumpReader)->rows($dumpPath, 'wp_', ['posts']));
        } finally {
            unlink($dumpPath);
        }
    }
}
