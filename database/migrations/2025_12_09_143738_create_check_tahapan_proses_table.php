<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckTahapanProsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_tahapan_proses', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('tahapan_id');
            $table->unsignedBigInteger('produksi_id');
            $table->string('keterangan')->nullable();
            $table->string('durasi')->nullable(); // in seconds
            $table->string('penanggung_jawab')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->enum('status', ['Belum Diperiksa', 'Diverifikasi'])->default('Belum Diperiksa');
            $table->foreign('tahapan_id')->references('id')->on('tahapan_proses')->onDelete('cascade');
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
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
        Schema::dropIfExists('check_tahapan_proses');
    }
}
