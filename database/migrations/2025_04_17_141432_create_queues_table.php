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
        Schema::create('queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_detail_id');
            $table->string('customer_name');
            $table->enum('status', ['booked', 'waiting', 'in_service', 'done', 'late', 'cancelled'])->default('booked');
            $table->integer('antrean');
            $table->timestamps();

            $table->foreign('booking_detail_id')->references('id')->on('booking_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
