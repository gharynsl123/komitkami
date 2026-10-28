<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_material', function (Blueprint $table) {
            $table->id();
            
            $table->string('jumlah')->nullable();
            $table->enum('persetujuan', ['pending', 'approved', 'rejected', 'done'])->default('pending');
            $table->string('keterangan')->nullable();
            $table->date('tanggal_di_proses')->nullable();

            $table->unsignedBigInteger('produksi_id')->unsigned()->nullable();
            $table->foreign('produksi_id')->references('id')->on('produksi')->onDelete('cascade');

            $table->unsignedBigInteger('inventory_id')->unsigned()->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');

            $table->unsignedBigInteger('formula_bahan_baku_id')->unsigned()->nullable();
            $table->foreign('formula_bahan_baku_id')->references('id')->on('formula_bahan_baku')->onDelete('cascade');

            $table->unsignedBigInteger('formula_bahan_kemas_id')->unsigned()->nullable();
            $table->foreign('formula_bahan_kemas_id')->references('id')->on('formula_bahan_kemas')->onDelete('cascade');
            
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('permintaan_material');
    }
}
