<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuestionType;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionTypes = [
            [
                'type' => 'multiple_choice',
                'description' => 'Multiple Choice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'true_false',
                'description' => 'True False',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'enumeration',
                'description' => 'Enumeration',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'short_answer',
                'description' => 'Short Answer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'essay',
                'description' => 'Essay',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'matching',
                'description' => 'Matching',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'fill_in_blank',
                'description' => 'Fill In Blank',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'drag_drop',
                'description' => 'Drag Drop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert question types, avoiding duplicates
        foreach ($questionTypes as $questionType) {
            QuestionType::firstOrCreate(
                ['type' => $questionType['type']],
                $questionType
            );
        }

        $this->command->info('Question types seeded successfully!');
    }
}
