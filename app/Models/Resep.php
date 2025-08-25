<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengguna;

class Resep extends Model
{
    protected $table = 'resep';
    protected $primaryKey = 'id_resep';

    protected $fillable = [
        'nama_pasien',
        'id_dokter',
        'tanggal_resep',
        'keterangan_sakit',
        'status'
    ];

    public function obats()
    {
        return $this->belongsToMany(Obat::class, 'detail_resep', 'id_resep', 'id_obat')->withPivot('jumlah', 'keterangan');
    }

    public function detail()
    {
        return $this->hasMany(DetailResep::class, 'id_resep', 'id_resep');
    }

    public function dokter()
    {
        return $this->belongsTo(Pengguna::class, 'id_dokter', 'id_pengguna');
    }


}
