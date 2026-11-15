<?php

namespace App\Support;

use Generator;
use RuntimeException;

class WordPressSqlDumpReader
{
    /**
     * @param  list<string>  $tableSuffixes
     * @return Generator<array{table: string, fields: array<string, string|null>}>
     */
    public function rows(string $path, string $prefix, array $tableSuffixes): Generator
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('File dump WordPress tidak ditemukan atau tidak dapat dibaca.');
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File dump WordPress tidak dapat dibaca.');
        }

        $allowedTables = array_fill_keys($tableSuffixes, true);
        $statement = '';
        $columns = [];
        $tableSuffix = null;

        try {
            while (($line = fgets($handle)) !== false) {
                if ($statement === '') {
                    if (! preg_match('/^INSERT INTO `([^`]+)` \(([^)]*)\) VALUES(?:\s|$)/', trim($line), $matches)) {
                        continue;
                    }

                    $tableName = $matches[1];
                    if (! str_starts_with($tableName, $prefix)) {
                        continue;
                    }

                    $tableSuffix = substr($tableName, strlen($prefix));
                    if (! isset($allowedTables[$tableSuffix])) {
                        $tableSuffix = null;

                        continue;
                    }

                    preg_match_all('/`([^`]+)`/', $matches[2], $columnMatches);
                    $columns = $columnMatches[1];
                    $statement = $line;
                } else {
                    $statement .= $line;
                }

                if ($statement !== '' && str_ends_with(rtrim($line), ';')) {
                    foreach ($this->parseRows($statement, $columns) as $fields) {
                        yield ['table' => $tableSuffix, 'fields' => $fields];
                    }

                    $statement = '';
                    $columns = [];
                    $tableSuffix = null;
                }
            }

            if ($statement !== '') {
                throw new RuntimeException('Dump WordPress berakhir sebelum satu pernyataan INSERT selesai.');
            }
        } finally {
            fclose($handle);
        }
    }

    public function detectPrefix(string $path): string
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File dump WordPress tidak dapat dibaca.');
        }

        try {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/^CREATE TABLE `(.+?)posts` \(/', trim($line), $matches)) {
                    return $matches[1];
                }
            }
        } finally {
            fclose($handle);
        }

        throw new RuntimeException('Tabel posts WordPress tidak ditemukan di dalam dump.');
    }

    /**
     * @param  list<string>  $columns
     * @return Generator<array<string, string|null>>
     */
    private function parseRows(string $statement, array $columns): Generator
    {
        $valuesPosition = strpos($statement, 'VALUES');

        if ($valuesPosition === false) {
            throw new RuntimeException('Bagian VALUES tidak ditemukan pada pernyataan INSERT.');
        }

        $sqlValues = substr($statement, $valuesPosition + strlen('VALUES'));
        $fields = [];
        $field = '';
        $depth = 0;
        $quote = null;
        $escaped = false;
        $length = strlen($sqlValues);

        for ($index = 0; $index < $length; $index++) {
            $character = $sqlValues[$index];

            if ($quote !== null) {
                $field .= $character;

                if ($escaped) {
                    $escaped = false;
                } elseif ($character === '\\') {
                    $escaped = true;
                } elseif ($character === $quote) {
                    if (($sqlValues[$index + 1] ?? null) === $quote) {
                        $field .= $sqlValues[++$index];
                    } else {
                        $quote = null;
                    }
                }

                continue;
            }

            if ($character === "'" || $character === '"') {
                $quote = $character;
                $field .= $character;
            } elseif ($character === '(') {
                $depth++;
                if ($depth === 1) {
                    $fields = [];
                    $field = '';
                } else {
                    $field .= $character;
                }
            } elseif ($character === ')') {
                $depth--;

                if ($depth === 0) {
                    $fields[] = trim($field);
                    if (count($fields) !== count($columns)) {
                        throw new RuntimeException('Jumlah kolom pada dump WordPress tidak sesuai.');
                    }

                    $row = array_combine($columns, array_map($this->decodeField(...), $fields));
                    if ($row === false) {
                        throw new RuntimeException('Kolom pada dump WordPress tidak dapat dipetakan.');
                    }

                    yield $row;
                } else {
                    $field .= $character;
                }
            } elseif ($character === ',' && $depth === 1) {
                $fields[] = trim($field);
                $field = '';
            } elseif ($depth > 0) {
                $field .= $character;
            }
        }

        if ($depth !== 0 || $quote !== null) {
            throw new RuntimeException('Format nilai pada dump WordPress tidak lengkap.');
        }
    }

    private function decodeField(string $field): ?string
    {
        $field = trim($field);

        if (strtoupper($field) === 'NULL') {
            return null;
        }

        if (! str_starts_with($field, "'") || ! str_ends_with($field, "'")) {
            return $field;
        }

        $value = substr($field, 1, -1);
        $decoded = '';
        $length = strlen($value);

        for ($index = 0; $index < $length; $index++) {
            $character = $value[$index];

            if ($character === '\\' && $index + 1 < $length) {
                $index++;
                $decoded .= match ($value[$index]) {
                    '0' => "\0",
                    'b' => "\x08",
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'Z' => "\x1a",
                    default => $value[$index],
                };
            } elseif ($character === "'" && ($value[$index + 1] ?? null) === "'") {
                $decoded .= "'";
                $index++;
            } else {
                $decoded .= $character;
            }
        }

        return $decoded;
    }
}
