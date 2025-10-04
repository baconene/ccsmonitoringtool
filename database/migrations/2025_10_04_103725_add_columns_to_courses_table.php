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
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('courses', 'instructor_name')) {
                $table->string('instructor_name')->nullable()->after('instructor_id');
            }
            if (!Schema::hasColumn('courses', 'duration')) {
                $table->integer('duration')->nullable()->after('description'); // Duration in minutes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Only drop columns we added
            if (Schema::hasColumn('courses', 'instructor_name')) {
                $table->dropColumn('instructor_name');
            }
            if (Schema::hasColumn('courses', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }
};
