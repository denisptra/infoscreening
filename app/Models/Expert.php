<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Database\Factories\ExpertFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    /** @use HasFactory<ExpertFactory> */
    use HasFactory, HasImageUrl;

    protected $fillable = [
        'name',
        'specialty',
        'city',
        'bio',
        'email',
        'phone',
        'photo',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->photo));
    }
}
