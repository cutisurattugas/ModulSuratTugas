<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuratTugasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('nomor_surat')->unique();
            $table->enum('jenis', ['individu', 'tim']);
            $table->enum('jarak', ['dalam_kota', 'luar_kota']); // Tambahkan ini
            $table->enum('status', ['diproses', 'disetujui', 'selesai']);
            $table->unsignedBigInteger('wadir2_id');
            $table->timestamp('tanggal_disetujui_wadir2')->nullable();
            $table->unsignedBigInteger('pimpinan_id');
            $table->timestamp('tanggal_disetujui_pimpinan')->nullable();
            $table->foreign('wadir2_id')->references('id')->on('pejabats')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pimpinan_id')->references('id')->on('pejabats')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('surat_tugas');
    }
}
