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
        Schema::create('inspeksi_proses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jumlah_sampel_uji')->nullable();
            $table->enum('kesimpulan', ['OK', 'NOT OK'])->nullable();
            $table->string('petugas_qc')->nullable();
            $table->unsignedBigInteger('produksi_id')->unsigned();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeksi_proses');
    }
};
