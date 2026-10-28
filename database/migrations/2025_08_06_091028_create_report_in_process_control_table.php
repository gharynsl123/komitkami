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
        Schema::create('report_in_process_control', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal_pengujian')->nullable();
            $table->string('disampling_oleh')->nullable();
            $table->string('jumlah_sampel_uji')->nullable();
            $table->enum('kesimpulan', ['OK', 'NOT OK', 'Lainnya'])->nullable();
            $table->string('catatan')->nullable();
            $table->unsignedBigInteger('produksi_id')->unsigned();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_in_process_control');
    }
};
