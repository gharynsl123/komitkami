<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTahapanProsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tahapan_proses', function (Blueprint $table) {
            $table->id();
            $table->text('nama_proses');
            $table->string('acuan');
            $table->unsignedBigInteger('formula_id');
            $table->foreign('formula_id')->references('id')->on('formula')->onDelete('cascade');
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
        Schema::dropIfExists('tahapan_proses');
    }
}
