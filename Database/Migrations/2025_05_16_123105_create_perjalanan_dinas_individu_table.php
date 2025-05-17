<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerjalananDinasIndividuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perjalanan_dinas_individu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perjalanan_dinas_id');
            $table->unsignedBigInteger('pegawai_id');
            $table->string('kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('tempat');
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
        Schema::dropIfExists('perjalanan_dinas_individu');
    }
}
