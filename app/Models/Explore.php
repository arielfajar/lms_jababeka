<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Explore extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_explore';
    protected $primaryKey = 'explore_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'judul',
        'tentang',
        'objective',
        'course',
        'trainer',
        'content_id',
        'img'
    ];
}
