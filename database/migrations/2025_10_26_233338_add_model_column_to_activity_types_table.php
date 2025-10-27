<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_types', function (Blueprint $table) {
            $table->string('model')->nullable()->after('name');
        });

        // Update existing records with their model paths
        DB::table('activity_types')->where('name', 'Quiz')->update(['model' => 'App\\Models\\Quiz']);
        DB::table('activity_types')->where('name', 'Assignment')->update(['model' => 'App\\Models\\Assignment']);
        DB::table('activity_types')->where('name', 'Assessment')->update(['model' => 'App\\Models\\Assessment']);
        DB::table('activity_types')->where('name', 'Exercise')->update(['model' => 'App\\Models\\Exercise']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_types', function (Blueprint $table) {
            $table->dropColumn('model');
        });
    }
};
