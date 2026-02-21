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
        // Only drop if it exists without foreign keys, or skip if tables don't exist yet
        if (Schema::hasTable('lesson_document')) {
            Schema::dropIfExists('lesson_document');
        }
        
        // Only create if both parent tables exist
        if (Schema::hasTable('lessons') && Schema::hasTable('documents')) {
            Schema::create('lesson_document', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
                $table->foreignId('document_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_document');
    }
};
