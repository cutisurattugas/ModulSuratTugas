<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaporanSuratTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_id');
            $table->string('file_laporan'); // Path file (PDF/doc)
            $table->enum('predikat_penilaian', ['Dibawah Ekspektasi', 'Sesuai Ekspektasi', 'Diatas Ekspektasi'])->nullable();
            $table->text('deskripsi_penilaian')->nullable();
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
        Schema::dropIfExists('laporan_surat_tugas');
    }
}
