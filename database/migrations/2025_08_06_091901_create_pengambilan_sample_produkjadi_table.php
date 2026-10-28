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
        Schema::create('pengambilan_sample_produkjadi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_sampling');
            $table->string('jumlah_sample_diambil');
            $table->string('peralatan_sampling');
            $table->string('lokasi_sampling');
            $table->string('catatan')->nullable();
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
        Schema::dropIfExists('pengambilan_sample_produkjadi');
    }
};
