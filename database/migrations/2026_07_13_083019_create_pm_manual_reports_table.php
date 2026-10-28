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
        Schema::create('pm_manual_reports', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('inventory_id');
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
            
            $table->unsignedBigInteger('suplier_material_id')->nullable();
            $table->foreign('suplier_material_id')->references('id')->on('suplier_material')->onDelete('cascade');
            
            $table->string('nomor_batch')->nullable();
            $table->date('tanggal_laporan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_manual_reports');
    }
};
