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
        Schema::create('riwayat_alat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produksi_id');
            $table->unsignedBigInteger('ruang_produksi_id');
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->foreign('ruang_produksi_id')->references('id')->on('ruang_produksi')->onDelete('cascade');
            $table->string('nama_alat');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_alat');
    }
};
