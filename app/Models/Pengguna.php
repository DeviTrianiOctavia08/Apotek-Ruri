<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'nama_lengkap',
        'kontak',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
