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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('barberman_id');
            $table->uuid('hairstyle_id')->nullable();
            $table->string('deskripsi')->nullable();
            $table->date('tanggal');
            $table->time('jam');
            $table->string('status')->default('pending'); // Status: pending, completed, canceled
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('barberman_id')->references('id')->on('barbermans')->onDelete('cascade');
            $table->foreign('hairstyle_id')->references('id')->on('hairstyles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
