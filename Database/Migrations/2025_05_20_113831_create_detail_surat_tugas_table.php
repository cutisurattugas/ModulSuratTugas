<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailSuratTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_id');
            $table->unsignedBigInteger('pegawai_id'); // Pegawai utama (untuk tim/individu)
            $table->string('kegiatan_maksud'); // Gabungan field 'kegiatan' (individu) dan 'maksud' (tim)
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('tempat');
            $table->string('alat_angkutan')->nullable(); // Hanya diisi jika jarak = luar_kota
            $table->integer('lama_perjalanan')->nullable(); // Hanya untuk tim
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
        Schema::dropIfExists('detail_surat_tugas');
    }
}
