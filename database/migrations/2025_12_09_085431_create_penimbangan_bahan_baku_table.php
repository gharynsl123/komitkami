<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenimbanganBahanBakuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penimbangan_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('no_batch');
            $table->decimal('hasil_timbang', 10, 3)->nullable();
            $table->string('operator_penimbangan')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->string('durasi')->default(0); // in seconds
            $table->enum('status', ['Belum Diperiksa', 'Diverifikasi'])->default('Belum Diperiksa');
            $table->unsignedBigInteger('produksi_id');
            $table->unsignedBigInteger('formula_bahan_baku_id');
            $table->foreign('formula_bahan_baku_id')->references('id')->on('formula_bahan_baku')->onDelete('cascade');
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penimbangan_bahan_baku');
    }
}
