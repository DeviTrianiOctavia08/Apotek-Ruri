<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');

            $table->unsignedBigInteger('id_obat')->nullable();
            $table->unsignedBigInteger('id_pengguna')->nullable();

            $table->enum('jenis', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->float('total');
            $table->date('tanggal_transaksi');
            $table->timestamps();

            $table->foreign('id_obat')->references('id_obat')->on('obat')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
