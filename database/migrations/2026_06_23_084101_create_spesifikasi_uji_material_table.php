<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spesifikasi_uji_material', function (Blueprint $table) {
            $table->id();
            $table->string('section_name');
            $table->string('parameter');
            $table->string('spesifikasi');

            // relasi kepada table inventory
            $table->unsignedBigInteger('inventory_id')->unsigned();
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');

            // relasi ke table produsen
            $table->unsignedBigInteger('manufacturer_id')->unsigned();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spesifikasi_uji_material');
    }
};
