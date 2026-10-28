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
        Schema::create('acuan_laporan_ipc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formula_id');
            $table->foreign('formula_id')->references('id')->on('formula')->onDelete('cascade');
            $table->string('section_name');
            $table->string('pemeriksaan');
            $table->string('spesifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acuan_laporan_ipc');
    }
};
