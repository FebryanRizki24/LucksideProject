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
        Schema::table('booking_details', function (Blueprint $table) {
            $table->uuid('booking_id')->nullable()->after('id');
            $table->uuid('user_id')->nullable()->after('booking_id');
            $table->uuid('service_id')->nullable()->after('customer_name');
            $table->uuid('barberman_id')->after('service_id');
            $table->uuid('hairstyle_id')->nullable()->after('barberman_id');

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('barberman_id')->references('id')->on('barbermans')->onDelete('cascade');
            $table->foreign('hairstyle_id')->references('id')->on('hairstyles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropForeign('booking_id');
            $table->dropForeign('user_id');
            $table->dropForeign('service_id');
            $table->dropForeign('barberman_id');
            $table->dropForeign('hairstyle_id');
        });
    }
};
