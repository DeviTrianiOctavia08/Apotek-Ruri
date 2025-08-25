<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');

            // Kolom foreign key
            $table->unsignedBigInteger('id_obat')->nullable();
            $table->unsignedBigInteger('id_pengguna')->nullable();

            // Data transaksi
            $table->string('jenis'); // masuk / keluar
            $table->integer('jumlah');
            $table->integer('total');
            $table->date('tanggal_transaksi')->nullable();
            $table->timestamps();

            // Foreign key ke tabel obat dan pengguna
            $table->foreign('id_obat')->references('id_obat')->on('obat')->onDelete('set null');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan');
    }
};
