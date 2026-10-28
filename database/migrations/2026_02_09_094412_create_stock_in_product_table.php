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
        Schema::create('stock_in_product', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->string('expired_date')->nullable();
            $table->string('note')->nullable();
            
            // sumber in
            $table->unsignedBigInteger('produksi_id')->constrained('produksi')->onDelete('cascade')->nullable();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');

            // di peruntukkan
            $table->unsignedBigInteger('product_id')->constrained('products')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_product');
    }
};
