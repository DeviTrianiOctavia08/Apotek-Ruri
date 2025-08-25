<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Obat;
use App\Models\Pengguna;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_obat',
        'id_pengguna',
        'jenis',
        'jumlah',
        'total',
        'tanggal_transaksi'
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
