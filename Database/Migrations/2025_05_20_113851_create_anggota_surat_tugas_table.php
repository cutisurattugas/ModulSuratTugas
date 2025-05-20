<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnggotaSuratTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggota_surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_id'); // Ganti dari surat_tugas_tim_id
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('surat_tugas_id')->references('id')->on('surat_tugas')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
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
        Schema::dropIfExists('anggota_surat_tugas');
    }
}
