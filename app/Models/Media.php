<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;
    use HasImageUrl;

    protected $table = 'media';

    protected $fillable = [
        'name',
        'file_path',
        'mime_type',
        'size',
        'width',
        'height',
        'caption',
        'alt_text',
    ];

    protected $appends = [
        'url',
        'formatted_size',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->resolveImageUrl($this->file_path) ?? '',
        );
    }

    protected function formattedSize(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $bytes = $this->size;
                if ($bytes <= 0 && Str::startsWith((string) $this->file_path, ['http://', 'https://', '//'])) {
                    return 'Eksternal';
                }
                if ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 1).' MB';
                }
                if ($bytes >= 1024) {
                    return number_format($bytes / 1024, 0).' KB';
                }

                return $bytes.' B';
            }
        );
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->mime_type ?? '', 'image/')
            || Str::endsWith(strtolower($this->file_path), ['.jpg', '.jpeg', '.png', '.webp', '.gif', '.svg']);
    }
}
