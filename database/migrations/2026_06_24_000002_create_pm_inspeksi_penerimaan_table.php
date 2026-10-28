<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmInspeksiPenerimaanTable extends Migration
{
    public function up()
    {
        Schema::create('pm_inspeksi_penerimaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id');
            $table->string('bagian')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->string('negara_asal')->nullable();
            $table->boolean('doc_coa')->default(false);
            $table->boolean('doc_msds')->default(false);
            $table->boolean('doc_halal')->default(false);
            $table->string('doc_lainnya')->nullable();
            
            $table->string('hasil_bentuk_atas')->nullable();
            $table->string('hasil_bentuk_tengah')->nullable();
            $table->string('hasil_bentuk_bawah')->nullable();
            
            $table->string('hasil_warna_atas')->nullable();
            $table->string('hasil_warna_tengah')->nullable();
            $table->string('hasil_warna_bawah')->nullable();
            
            $table->string('hasil_rasa_atas')->nullable();
            $table->string('hasil_rasa_tengah')->nullable();
            $table->string('hasil_rasa_bawah')->nullable();
            
            $table->string('kesimpulan')->nullable();
            $table->string('petugas_qc')->nullable();
            $table->timestamps();

            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pm_inspeksi_penerimaan');
    }
}
