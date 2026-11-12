<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasImageUrl;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'buy_url',
        'is_available',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_available' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->slug = $product->slug ?: Str::slug($product->name.'-'.Str::random(4));
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->image));
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::get(fn () => 'Rp '.number_format($this->price, 0, ',', '.'));
    }
}
