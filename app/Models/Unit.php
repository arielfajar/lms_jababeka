<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'tbl_unit';
    protected $primaryKey = 'unit_id';
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'unit_nama',
        'unit_slug',
        'divisi'
    ];
    

}
