<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerjalananDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('nomor_surat')->unique();
            $table->unsignedBigInteger('pejabat_id');
            $table->enum('jenis', ['individu', 'tim']);
            $table->foreign('pejabat_id')->references('id')->on('pejabats')->onDelete('cascade');
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
        Schema::dropIfExists('perjalanan_dinas');
    }
}
