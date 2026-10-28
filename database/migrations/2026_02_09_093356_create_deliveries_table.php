<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_terbit');
            $table->string('slug')->unique();
            $table->string('no_sj')->unique();
            $table->string('note')->nullable();
            $table->string('photo_invoice')->nullable();
            $table->string('photo_surat_jalan')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'delivered', 'canceled'])->default('in_progress');

            // relasi ke inovice
            $table->unsignedBigInteger('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoice')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
