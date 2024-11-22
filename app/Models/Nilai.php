<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_nilai';
    protected $primaryKey = 'nilai_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'content_id',
        'user_id',
        'jenis',
        'jawaban_pilihan',
        'nilai',
        'progress'
    ];
    protected $casts = [
        'jawaban_pilihan' => 'array',
    ];
    

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'content_id'); // Adjust the foreign key and local key as necessary
    }
    

}
