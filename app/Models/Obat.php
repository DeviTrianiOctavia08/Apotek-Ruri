<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = "obat";
    protected $primaryKey = 'id_obat';

    protected $fillable = [
        "nama_obat",
        "stok",
        "harga",
        "tanggal_kadaluarsa",
        "tanggal_masuk",
        "tanggal_keluar",
        "status",
    ];

    protected $dates =[
        "tanggal_kadaluarsa", 
        "tanggal_masuk", 
        "tanggal_keluar"
    ];

    //algoritma FEFO
    public function scopeFefo($query)
    {
        return $query->where('stok', '>', 0)
                     ->orderBy('tanggal_kadaluarsa', 'asc');
    }

    public function parentObat()
    {
        return $this->belongsTo(Obat::class, 'id_obat_asal');
    }

    public function transaksi(){
        return $this->hasMany(Transaksi::class, 'id_obat', 'id_obat');
    }

}

