<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Make sure to import the correct class
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable // Extend Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'tbl_user';
    public $timestamps = false; 
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'nik',
        'role_id',
        'nama_lengkap', // Assuming you have a name column
        'username',
        'email',
        'password',
        'foto',
        'divisi',
        'jabatan',
        'approval_line',
        'join_date',
        'no_telp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
