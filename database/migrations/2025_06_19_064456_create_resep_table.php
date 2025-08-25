<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->bigIncrements('id_resep'); 
            $table->unsignedBigInteger('id_dokter');
            $table->string('nama_pasien');
            $table->text('keterangan_sakit')->nullable();
            $table->date('tanggal_resep');
            $table->enum('status', ['belum', 'diproses', 'selesai'])->default('belum');
            $table->timestamps();

            $table->foreign('id_dokter')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resep');
    }
};
