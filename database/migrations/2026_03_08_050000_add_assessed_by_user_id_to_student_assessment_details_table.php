<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_assessment_details', function (Blueprint $table) {
            $table->foreignId('assessed_by_user_id')
                ->nullable()
                ->after('course_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index('assessed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_assessment_details', function (Blueprint $table) {
            $table->dropForeign(['assessed_by_user_id']);
            $table->dropIndex(['assessed_by_user_id']);
            $table->dropColumn('assessed_by_user_id');
        });
    }
};
