<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaporanPerjalananDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perjalanan_dinas_id'); // FK
            $table->string('file_laporan'); // direktori / path file (contoh: 'laporan/123.pdf')
            $table->enum('penilaian', ['Dibawah Ekspektasi', 'Sesuai Ekspektasi', 'Diatas Ekspektasi'])->nullable();
            $table->foreign('perjalanan_dinas_id')->references('id')->on('perjalanan_dinas')->onDelete('cascade');
            $table->timestamp('tanggal_upload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_perjalanan_dinas');
    }
}
