<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, HasImageUrl;

    public const TYPES = ['Festival', 'Pemutaran', 'Diskusi', 'Workshop', 'Pameran', 'Lainnya'];

    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'venue',
        'city',
        'starts_at',
        'ends_at',
        'image',
        'url',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            $event->slug = $event->slug ?: Str::slug($event->title.'-'.Str::random(4));
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $inner) {
            $inner->where('starts_at', '>=', now()->startOfDay())
                ->orWhere('ends_at', '>=', now());
        });
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->image));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
