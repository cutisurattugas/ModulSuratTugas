<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerjalananDinasTimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perjalanan_dinas_tim', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perjalanan_dinas_id'); // FK
            $table->unsignedBigInteger('pegawai_id'); // Pegawai utama
            $table->string('maksud');
            $table->string('alat_angkutan');
            $table->integer('lama_perjalanan'); // dalam hari
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->foreign('perjalanan_dinas_id')->references('id')->on('perjalanan_dinas')->onDelete('cascade');
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
        Schema::dropIfExists('perjalanan_dinas_tim');
    }
}
