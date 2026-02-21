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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('difficulty_level', ['basic', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            $table->decimal('weight', 5, 2)->default(1.0); // Weight for skill importance
            $table->decimal('competency_threshold', 5, 2)->default(70.0); // Minimum score to pass
            $table->enum('bloom_level', ['remember', 'understand', 'apply', 'analyze', 'evaluate', 'create'])->nullable();
            $table->json('tags')->nullable(); // e.g., ["critical thinking", "problem solving"]
            $table->timestamps();

            $table->foreign('module_id')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');

            $table->index('module_id');
            $table->index('difficulty_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
