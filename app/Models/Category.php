<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasImageUrl;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'hero_image',
        'color',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->slug ??= Str::slug($category->name);
        });

        static::updating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    protected function heroImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->hero_image));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
