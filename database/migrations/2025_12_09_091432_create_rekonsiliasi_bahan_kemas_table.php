<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekonsiliasiBahanKemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekonsiliasi_bahan_kemas', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('produksi_id');
            $table->string('nama_barang')->nullable();
            $table->string('tersedia')->nullable();
            $table->integer('terpakai');
            $table->string('keterangan');
            $table->string('operator')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->enum('status', ['Belum Diperiksa', 'Diverifikasi'])->default('Belum Diperiksa');
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->unsignedBigInteger('formula_bahan_kemas_id')->nullable();
            $table->foreign('formula_bahan_kemas_id')->references('id')->on('formula_bahan_kemas')->onDelete('cascade');
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
        Schema::dropIfExists('rekonsiliasi_bahan_kemas');
    }
}
