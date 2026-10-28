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
        Schema::create('pengujian_produk_jadi', function (Blueprint $table) {
            $table->id();
            $table->string('bagian');
            $table->date('tanggal');
            $table->string('petugas_qc')->nullable();
            $table->enum('kesimpulan', ['OK', 'NOT OK'])->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('pengujian_produk_jadi');
    }
};
