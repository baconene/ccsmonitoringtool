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
        Schema::create('grade_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('setting_type', ['module_component', 'activity_type'])->comment('Type of grading setting');
            $table->string('setting_key')->comment('Unique key: lessons, activities, Quiz, Assignment, etc.');
            $table->string('display_name')->comment('Human-readable name');
            $table->decimal('weight_percentage', 5, 2)->default(0)->comment('Weight percentage (0-100)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['setting_type', 'setting_key']);
            $table->index('is_active');
        });
        
        // Insert default settings
        $this->insertDefaultSettings();
    }

    /**
     * Insert default grade settings
     */
    private function insertDefaultSettings(): void
    {
        $now = now();
        
        DB::table('grade_settings')->insert([
            // Module Component Weights (Lessons vs Activities)
            [
                'setting_type' => 'module_component',
                'setting_key' => 'lessons',
                'display_name' => 'Lessons Weight',
                'weight_percentage' => 20.00,
                'description' => 'Weight of lessons in module grade calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_type' => 'module_component',
                'setting_key' => 'activities',
                'display_name' => 'Activities Weight',
                'weight_percentage' => 80.00,
                'description' => 'Weight of activities in module grade calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Activity Type Weights (within the Activities portion)
            [
                'setting_type' => 'activity_type',
                'setting_key' => 'Quiz',
                'display_name' => 'Quiz Weight',
                'weight_percentage' => 30.00,
                'description' => 'Weight of quizzes in activity score calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_type' => 'activity_type',
                'setting_key' => 'Assignment',
                'display_name' => 'Assignment Weight',
                'weight_percentage' => 15.00,
                'description' => 'Weight of assignments in activity score calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_type' => 'activity_type',
                'setting_key' => 'Assessment',
                'display_name' => 'Assessment Weight',
                'weight_percentage' => 35.00,
                'description' => 'Weight of assessments in activity score calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'setting_type' => 'activity_type',
                'setting_key' => 'Exercise',
                'display_name' => 'Exercise Weight',
                'weight_percentage' => 20.00,
                'description' => 'Weight of exercises in activity score calculation',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_settings');
    }
};
