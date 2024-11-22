<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_content';
    protected $primaryKey = 'content_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'judul',
        'explore_id',
        'jenis'
    ];

    public function explore()
    {
        return $this->belongsTo(Explore::class, 'explore_id', 'explore_id'); // Adjust the foreign key and local key as necessary
    }

}
