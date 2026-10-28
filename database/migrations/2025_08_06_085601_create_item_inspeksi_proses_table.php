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
        Schema::create('item_inspeksi_proses', function (Blueprint $table) {
            $table->id();
            $table->string('inspeksi_bagian_atas')->nullable();
            $table->string('inspeksi_bagian_bawah')->nullable();
            // relasi acuan_laporan_ipc
            $table->unsignedBigInteger('produksi_id')->unsigned();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');

            $table->unsignedBigInteger('acuan_laporan_ipc_id')->unsigned();
            $table->foreign('acuan_laporan_ipc_id')->references('id')->on('acuan_laporan_ipc')->onDelete('cascade');
            
            $table->unsignedBigInteger('inspeksi_proses_id')->unsigned();
            $table->foreign('inspeksi_proses_id')->references('id')->on('inspeksi_proses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_ispeksi_proses');
    }
};
