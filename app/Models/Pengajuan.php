<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_pengajuan';
    protected $primaryKey = 'pengajuan_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'nik',
        'jabatan',
        'nama',
        'divisi',
        'tanggal_masuk',
        'email',
        'judul_training',
        'penyelenggara',
        'tujuan_training',
        'harapan_training',
        'biaya',
        'hari_tanggal',
        'jam',
        'tempat',
        'approval_line',
        'ttd',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id'); // Define the relationship with the User model
    }
}
