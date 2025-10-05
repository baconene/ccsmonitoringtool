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
        Schema::create('module_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('module_course_id')->nullable(); // Reference to the course through module
            $table->integer('order')->default(0); // Order of the activity within the module
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate activity in the same module
            $table->unique(['module_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_activities');
    }
};
