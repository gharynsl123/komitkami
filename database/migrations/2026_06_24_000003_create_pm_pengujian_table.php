<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmPengujianTable extends Migration
{
    public function up()
    {
        Schema::create('pm_pengujian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_in_id');
            $table->date('tanggal')->nullable();
            $table->string('kesimpulan')->nullable();
            $table->string('petugas_qc')->nullable();
            $table->timestamps();

            $table->foreign('stock_in_id')->references('id')->on('stock_in')->onDelete('cascade');
        });

        Schema::create('pm_pengujian_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_pengujian_id');
            $table->string('parameter_uji')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('hasil')->nullable();
            $table->timestamps();

            $table->foreign('pm_pengujian_id')->references('id')->on('pm_pengujian')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pm_pengujian_detail');
        Schema::dropIfExists('pm_pengujian');
    }
}
