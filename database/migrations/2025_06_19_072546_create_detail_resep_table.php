<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_resep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_resep');
            $table->unsignedBigInteger('id_obat');
            $table->integer('jumlah');
            $table->string('keterangan');
            $table->foreign('id_resep')->references('id_resep')->on('resep')->onDelete('cascade');
            $table->foreign('id_obat')->references('id_obat')->on('obat')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::dropIfExists('detail_resep');
    }
};
