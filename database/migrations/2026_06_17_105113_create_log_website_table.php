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
        Schema::create('log_website', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('keterangan');

            // relasi id user
            $table->unsignedBigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_website');
    }
};
