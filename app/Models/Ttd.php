<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ttd extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_ttd';
    protected $primaryKey = 'ttd_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'nama',
        'img',
        'jabatan'
    ];
    

}
