<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengikutPerjalananDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengikut_perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perjalanan_dinas_tim_id');
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('perjalanan_dinas_tim_id')->references('id')->on('perjalanan_dinas_tim')->onDelete('cascade');
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
        Schema::dropIfExists('pengikut_perjalanan_dinas');
    }
}
