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
        Schema::create('stock_out', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah', 10, 3);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_keluar');

            // relasi
            $table->unsignedBigInteger('inventory_id')->unsigned();
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('stock_in_id')->unsigned()->nullable();
            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
            $table->unsignedBigInteger('permintaan_id')->unsigned()->nullable();
            $table->foreign('permintaan_id')->references('id')->on('permintaan_material')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out');
    }
};
