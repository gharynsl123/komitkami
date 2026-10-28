<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_control', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formula_id');
            $table->foreign('formula_id')->references('id')->on('formula')->onDelete('cascade');
            $table->string('parameter_pengujian');
            $table->string('nilai_standart');
            $table->integer('urutan')->nullable();
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
        Schema::dropIfExists('quality_control');
    }
}
