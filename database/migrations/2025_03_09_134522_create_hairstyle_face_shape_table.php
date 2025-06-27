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
        Schema::create('hairstyle_face_shape', function (Blueprint $table) {
            $table->uuid('hairstyle_id');
            $table->uuid('face_shape_id');
            $table->foreign('hairstyle_id')->references('id')->on('hairstyles')->onDelete('cascade');
            $table->foreign('face_shape_id')->references('id')->on('face_shapes')->onDelete('cascade');
            $table->primary(['hairstyle_id', 'face_shape_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hairstyle_face_shape');
    }
};
