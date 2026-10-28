<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->string('jenis');
            $table->string('ukuran');
            $table->string('code')->nullable();
            $table->string('stok')->nullable();
            $table->string('tipe')->nullable();
            $table->enum('kategori', ['PKRT', 'alkes'])->nullable();
            $table->unsignedBigInteger('brand_id')->unsigned();
            $table->foreign('brand_id')->references('id')->on('brand')->onDelete('cascade');

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
        Schema::dropIfExists('product');
    }
}
