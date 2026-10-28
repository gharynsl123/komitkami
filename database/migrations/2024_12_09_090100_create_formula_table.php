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
        Schema::create('formula', function (Blueprint $table) {
            $table->id();
            $table->string('nama_formula');
            $table->string('slug');
            $table->enum('tipe', ['bahan_kemas', 'bahan_baku']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formula');
    }
};
