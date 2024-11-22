<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_sertifikat';
    protected $primaryKey = 'sertifikat_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'explore_id',
        'user_id',
        'tanggal'
    ];

    public function explore()
    {
        return $this->belongsTo(Explore::class, 'explore_id', 'explore_id'); // Adjust the foreign key and local key as necessary
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'user_id'); // Define the relationship with the User model
}
// Sertifikat Model
public function ttd()
{
    return $this->belongsTo(Ttd::class, 'ttd_id'); // Adjust foreign key as necessary
}

}
