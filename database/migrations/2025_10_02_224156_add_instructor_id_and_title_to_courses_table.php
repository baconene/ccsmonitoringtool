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
        Schema::table('courses', function (Blueprint $table) {
            // Add instructor_id foreign key (references instructors table, not users)
            $table->unsignedBigInteger('instructor_id')->nullable()->after('description');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            
            // Add title field (keeping name for backward compatibility)
            $table->string('title')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn(['instructor_id', 'title']);
        });
    }
};
