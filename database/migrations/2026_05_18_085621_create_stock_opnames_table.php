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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id')->unsigned();
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
            $table->unsignedBigInteger('stock_in_id')->unsigned()->nullable();
            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
            $table->decimal('stok_sistem', 10, 3);
            $table->decimal('stok_fisik', 10, 3);
            $table->decimal('selisih', 10, 3);
            $table->date('tanggal_opname');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
