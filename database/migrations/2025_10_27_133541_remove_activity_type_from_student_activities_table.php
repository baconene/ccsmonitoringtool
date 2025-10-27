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
        Schema::table('student_activities', function (Blueprint $table) {
            // Drop indexes that include activity_type
            $table->dropIndex(['activity_type', 'status']);
            // Drop the column
            $table->dropColumn('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_activities', function (Blueprint $table) {
            $table->string('activity_type')->nullable()->after('activity_id');
            $table->index(['activity_type', 'status']);
        });
    }
};
