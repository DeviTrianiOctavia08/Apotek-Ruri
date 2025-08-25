<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id('id_obat');
            $table->unsignedBigInteger('id_obat_asal')->nullable();
            $table->string('nama_obat');
            $table->integer('stok');
            $table->float('harga');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')-> nullable();
            $table->date('tanggal_kadaluarsa');
            // $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('obat');
    }
};