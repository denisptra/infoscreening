<?php

namespace App\Models;

use Database\Factories\ContributionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    /** @use HasFactory<ContributionFactory> */
    use HasFactory;

    public const TYPES = ['Artikel', 'Agenda / Event', 'Liputan', 'Foto', 'Lainnya'];

    public const STATUSES = ['pending' => 'Menunggu', 'approved' => 'Diterima', 'rejected' => 'Ditolak'];

    protected $fillable = [
        'name',
        'email',
        'type',
        'title',
        'message',
        'link',
        'status',
    ];
}
