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
        Schema::create('module_lesson_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_lesson_id');
            $table->unsignedBigInteger('activity_id');
            $table->integer('order')->default(0); // Order of the activity within the lesson
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('module_lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['module_lesson_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_lesson_activities');
    }
};
