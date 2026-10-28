<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulaBahanBakuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formula_bahan_baku', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('inventory_id');
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');

            $table->unsignedBigInteger('formula_id');
            $table->foreign('formula_id')->references('id')->on('formula')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->decimal('jumlah', 10, 3)->nullable();
            $table->string('satuan')->nullable();
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
        Schema::dropIfExists('formula_bahan_baku');
    }
}
