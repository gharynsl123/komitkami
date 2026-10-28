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
        Schema::create('stock_out_product', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('note')->nullable();

            // tujuan out
            $table->unsignedBigInteger('delivery_item_id')->constrained('delivery_items')->onDelete('cascade');
            $table->foreign('delivery_item_id')->references('id')->on('delivery_items')->onDelete('cascade');

            // tujuan out
            $table->unsignedBigInteger('stock_in_product_id')->constrained('stock_in_product')->onDelete('cascade');
            $table->foreign('stock_in_product_id')->references('id')->on('stock_in_product')->onDelete('cascade');

            // di peruntukkan
            $table->unsignedBigInteger('product_id')->constrained('product')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_product');
    }
};
