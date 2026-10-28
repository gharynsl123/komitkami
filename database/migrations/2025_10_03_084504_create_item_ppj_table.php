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
        Schema::create('item_ppj', function (Blueprint $table) {
            $table->id();
            $table->string('hasil')->nullable();
            // relasi acuan_laporan_ipc
            $table->unsignedBigInteger('produksi_id')->unsigned();
            $table->unsignedBigInteger('acuan_laporan_ipc_id')->unsigned();
            $table->unsignedBigInteger('pengujian_produk_jadi_id')->unsigned();

            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->foreign('acuan_laporan_ipc_id')->references('id')->on('acuan_laporan_ipc')->onDelete('cascade');
            $table->foreign('pengujian_produk_jadi_id')->references('id')->on('pengujian_produk_jadi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_ppj');
    }
};
