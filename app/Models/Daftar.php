<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daftar extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_daftar';
    protected $primaryKey = 'daftar_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'explore_id',
        'status'
    ];

    public function explore()
    {
        return $this->belongsTo(Explore::class, 'explore_id', 'explore_id'); // Adjust the foreign key and local key as necessary
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'user_id');
}

}
