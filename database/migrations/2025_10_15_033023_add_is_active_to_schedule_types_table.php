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
        Schema::table('schedule_types', function (Blueprint $table) {
            if (!Schema::hasColumn('schedule_types', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_types', function (Blueprint $table) {
            if (Schema::hasColumn('schedule_types', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
