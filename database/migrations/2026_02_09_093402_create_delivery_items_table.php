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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kendaraan')->nullable();
            $table->string('nomor_kendaraan')->nullable();
            $table->string('jumlah_barang')->nullable();
            $table->string('note')->nullable();

            $table->unsignedBigInteger('delivery_id')->unsigned();
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');

            $table->unsignedBigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('order')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
