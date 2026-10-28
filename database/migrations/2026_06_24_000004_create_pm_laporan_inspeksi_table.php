<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmLaporanInspeksiTable extends Migration
{
    public function up()
    {
        Schema::create('pm_laporan_inspeksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id');
            $table->date('tanggal_efektif')->nullable();
            $table->date('tanggal_inspeksi')->nullable();
            $table->string('jumlah_material')->nullable();
            $table->string('jumlah_sampel')->nullable();
            $table->string('disiapkan_oleh')->nullable();
            $table->string('disetujui_oleh')->nullable();
            $table->string('diinspeksi_oleh')->nullable();
            $table->string('diperiksa_oleh')->nullable();
            $table->string('kesimpulan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
        });

        Schema::create('pm_laporan_inspeksi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_laporan_inspeksi_id');
            $table->string('parameter')->nullable();
            $table->string('persyaratan')->nullable();
            $table->string('hasil_atas')->nullable();
            $table->string('hasil_tengah')->nullable();
            $table->string('hasil_bawah')->nullable();
            $table->string('hasil_inspeksi')->nullable();
            $table->timestamps();

            $table->foreign('pm_laporan_inspeksi_id')->references('id')->on('pm_laporan_inspeksi')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pm_laporan_inspeksi_detail');
        Schema::dropIfExists('pm_laporan_inspeksi');
    }
}
