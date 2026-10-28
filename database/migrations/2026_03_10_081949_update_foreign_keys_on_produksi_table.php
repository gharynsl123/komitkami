<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysOnProduksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produksi', function (Blueprint $table) {
            // Drop old constraints
            $table->dropForeign(['formula_bahan_baku_id']);
            $table->dropForeign(['formula_bahan_kemas_id']);

            // Point constraints to the new formulas table
            $table->foreign('formula_bahan_baku_id')
                  ->references('id')->on('formula')
                  ->onDelete('set null');

            $table->foreign('formula_bahan_kemas_id')
                  ->references('id')->on('formula')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produksi', function (Blueprint $table) {
            // Drop the new constraints
            $table->dropForeign(['formula_bahan_baku_id']);
            $table->dropForeign(['formula_bahan_kemas_id']);

            // Reverse back to old constraints (assuming they existed)
            $table->foreign('formula_bahan_baku_id')
                  ->references('id')->on('formula_bahan_baku')
                  ->onDelete('cascade');

            $table->foreign('formula_bahan_kemas_id')
                  ->references('id')->on('formula_bahan_kemas')
                  ->onDelete('cascade');
        });
    }
}
