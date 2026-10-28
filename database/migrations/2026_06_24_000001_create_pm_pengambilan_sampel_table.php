<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmPengambilanSampelTable extends Migration
{
    public function up()
    {
        Schema::create('pm_pengambilan_sampel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id');
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->unsignedBigInteger('suplier_material_id')->nullable();

            $table->boolean('doc_coa')->default(false);
            $table->boolean('doc_msds')->default(false);
            $table->boolean('doc_halal')->default(false);
            $table->string('doc_lainnya')->nullable();

            $table->date('tanggal_sampling');
            $table->string('kategori_material')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('referensi_metode')->nullable();
            $table->string('jumlah_sampel')->nullable();
            $table->string('peralatan_sampling')->nullable();
            $table->string('lokasi_sampling')->nullable();
            $table->text('catatan')->nullable();
            $table->string('disampling_oleh')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->timestamps();

            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pm_pengambilan_sampel');
    }
}
