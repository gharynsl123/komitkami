<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sample_in_proses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_sampling');
            $table->string('tahapan_proses')->nullable();
            $table->unsignedBigInteger('produksi_id')->nullable()->unsigned();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->string('referensi_metode_sampling')->nullable();
            $table->string('jumlah_sample_diambil_bagian_atas')->nullable();
            $table->string('jumlah_sample_diambil_bagian_bawah')->nullable();
            $table->string('peralatan_sampling')->nullable();
            $table->string('lokasi_sampling')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_in_proses');
    }
};
