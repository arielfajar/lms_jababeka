<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_materi';
    protected $primaryKey = 'materi_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'content_id',
        'judul',
        'link'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'content_id'); // Adjust the foreign key and local key as necessary
    }

}
