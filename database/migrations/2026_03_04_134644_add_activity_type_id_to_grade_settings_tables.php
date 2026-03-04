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
        // Add activity_type_id to grade_settings table
        Schema::table('grade_settings', function (Blueprint $table) {
            $table->foreignId('activity_type_id')->nullable()->after('setting_key')->constrained('activity_types')->onDelete('cascade');
            $table->index('activity_type_id');
        });

        // Add activity_type_id to course_grade_settings table
        Schema::table('course_grade_settings', function (Blueprint $table) {
            $table->foreignId('activity_type_id')->nullable()->after('setting_key')->constrained('activity_types')->onDelete('cascade');
            $table->index('activity_type_id');
        });

        // Populate activity_type_id for existing records based on setting_key matching activity_types.name
        $activityTypes = DB::table('activity_types')->get();
        
        foreach ($activityTypes as $activityType) {
            // Update grade_settings
            DB::table('grade_settings')
                ->where('setting_type', 'activity_type')
                ->where('setting_key', $activityType->name)
                ->update(['activity_type_id' => $activityType->id]);

            // Update course_grade_settings
            DB::table('course_grade_settings')
                ->where('setting_type', 'activity_type')
                ->where('setting_key', $activityType->name)
                ->update(['activity_type_id' => $activityType->id]);
        }

        // Create default grade settings for any activity types that don't have settings yet
        $existingKeys = DB::table('grade_settings')
            ->where('setting_type', 'activity_type')
            ->pluck('setting_key')
            ->toArray();

        foreach ($activityTypes as $activityType) {
            if (!in_array($activityType->name, $existingKeys)) {
                DB::table('grade_settings')->insert([
                    'setting_type' => 'activity_type',
                    'setting_key' => $activityType->name,
                    'activity_type_id' => $activityType->id,
                    'display_name' => $activityType->name . ' Weight',
                    'weight_percentage' => 0.00, // Default to 0, admin needs to configure
                    'description' => "Weight of {$activityType->name} in activity score calculation",
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_settings', function (Blueprint $table) {
            $table->dropForeign(['activity_type_id']);
            $table->dropIndex(['activity_type_id']);
            $table->dropColumn('activity_type_id');
        });

        Schema::table('course_grade_settings', function (Blueprint $table) {
            $table->dropForeign(['activity_type_id']);
            $table->dropIndex(['activity_type_id']);
            $table->dropColumn('activity_type_id');
        });
    }
};
