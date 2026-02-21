<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Module;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all modules
        $modules = Module::with('course')->get();

        if ($modules->isEmpty()) {
            $this->command->info('No modules found. Please run course and module seeders first.');
            return;
        }

        // Define skill templates by module type
        $skillTemplates = [
            'math' => [
                [
                    'name' => 'Basic Arithmetic',
                    'description' => 'Understanding and performing basic mathematical operations',
                    'difficulty_level' => 'basic',
                    'bloom_level' => 'remember',
                    'weight' => 10,
                    'competency_threshold' => 70,
                    'tags' => ['computation', 'fundamentals'],
                ],
                [
                    'name' => 'Problem Solving',
                    'description' => 'Applying mathematical concepts to solve real-world problems',
                    'difficulty_level' => 'intermediate',
                    'bloom_level' => 'apply',
                    'weight' => 20,
                    'competency_threshold' => 75,
                    'tags' => ['critical thinking', 'application'],
                ],
                [
                    'name' => 'Mathematical Reasoning',
                    'description' => 'Analyzing and evaluating mathematical arguments and proofs',
                    'difficulty_level' => 'advanced',
                    'bloom_level' => 'analyze',
                    'weight' => 25,
                    'competency_threshold' => 80,
                    'tags' => ['logic', 'analysis'],
                ],
            ],
            'science' => [
                [
                    'name' => 'Scientific Knowledge',
                    'description' => 'Understanding fundamental scientific concepts and principles',
                    'difficulty_level' => 'basic',
                    'bloom_level' => 'understand',
                    'weight' => 15,
                    'competency_threshold' => 70,
                    'tags' => ['knowledge', 'comprehension'],
                ],
                [
                    'name' => 'Experimental Design',
                    'description' => 'Designing and conducting scientific experiments',
                    'difficulty_level' => 'intermediate',
                    'bloom_level' => 'apply',
                    'weight' => 25,
                    'competency_threshold' => 75,
                    'tags' => ['experimentation', 'methodology'],
                ],
                [
                    'name' => 'Data Analysis',
                    'description' => 'Analyzing and interpreting scientific data',
                    'difficulty_level' => 'advanced',
                    'bloom_level' => 'analyze',
                    'weight' => 20,
                    'competency_threshold' => 78,
                    'tags' => ['analysis', 'statistics'],
                ],
            ],
            'english' => [
                [
                    'name' => 'Reading Comprehension',
                    'description' => 'Understanding and analyzing written texts',
                    'difficulty_level' => 'basic',
                    'bloom_level' => 'understand',
                    'weight' => 15,
                    'competency_threshold' => 70,
                    'tags' => ['reading', 'comprehension'],
                ],
                [
                    'name' => 'Writing Skills',
                    'description' => 'Composing clear, well-structured written work',
                    'difficulty_level' => 'intermediate',
                    'bloom_level' => 'apply',
                    'weight' => 25,
                    'competency_threshold' => 75,
                    'tags' => ['communication', 'writing'],
                ],
                [
                    'name' => 'Critical Analysis',
                    'description' => 'Analyzing and evaluating literary works and arguments',
                    'difficulty_level' => 'advanced',
                    'bloom_level' => 'analyze',
                    'weight' => 25,
                    'competency_threshold' => 78,
                    'tags' => ['critical thinking', 'analysis'],
                ],
            ],
            'default' => [
                [
                    'name' => 'Content Mastery',
                    'description' => 'Understanding and retention of module content',
                    'difficulty_level' => 'intermediate',
                    'bloom_level' => 'understand',
                    'weight' => 20,
                    'competency_threshold' => 75,
                    'tags' => ['core', 'fundamentals'],
                ],
                [
                    'name' => 'Application',
                    'description' => 'Applying learned concepts in practice',
                    'difficulty_level' => 'intermediate',
                    'bloom_level' => 'apply',
                    'weight' => 25,
                    'competency_threshold' => 75,
                    'tags' => ['practical', 'application'],
                ],
                [
                    'name' => 'Advanced Thinking',
                    'description' => 'Higher-order thinking and synthesis',
                    'difficulty_level' => 'advanced',
                    'bloom_level' => 'create',
                    'weight' => 20,
                    'competency_threshold' => 80,
                    'tags' => ['advanced', 'synthesis'],
                ],
            ],
        ];

        $skillsCreated = 0;
        $skillActivityLinksCreated = 0;

        foreach ($modules as $module) {
            // Determine skill template based on course/module type
            $template = $this->getSkillTemplate($module, $skillTemplates);

            // Create skills for this module
            foreach ($template as $skillData) {
                $skill = Skill::firstOrCreate(
                    [
                        'module_id' => $module->id,
                        'name' => $skillData['name'],
                    ],
                    [
                        'description' => $skillData['description'],
                        'difficulty_level' => $skillData['difficulty_level'],
                        'weight' => $skillData['weight'],
                        'competency_threshold' => $skillData['competency_threshold'],
                        'bloom_level' => $skillData['bloom_level'],
                        'tags' => $skillData['tags'],
                    ]
                );

                $skillsCreated++;

                // Link activities to this skill
                $activities = Activity::inRandomOrder()->limit(random_int(2, 4))->get();

                foreach ($activities as $activity) {
                    // Check if already linked
                    $exists = $skill->activities()
                        ->where('activity_id', $activity->id)
                        ->exists();

                    if (!$exists) {
                        $skill->activities()->attach($activity->id, [
                            'weight' => rand(80, 100) / 100, // Random weight 0.8-1.0
                        ]);
                        $skillActivityLinksCreated++;
                    }
                }
            }
        }

        $this->command->info("Created {$skillsCreated} skills with {$skillActivityLinksCreated} activity links.");
    }

    /**
     * Get skill template based on module/course type
     */
    private function getSkillTemplate($module, $skillTemplates): array
    {
        $course = $module->course;
        $courseTitle = strtolower($course?->title ?? '');
        $moduleName = strtolower($module->title ?? '');

        // Try to match course/module title
        if (str_contains($courseTitle, 'math')) {
            return $skillTemplates['math'];
        } elseif (str_contains($courseTitle, 'science')) {
            return $skillTemplates['science'];
        } elseif (str_contains($courseTitle, 'english') || str_contains($courseTitle, 'language')) {
            return $skillTemplates['english'];
        }

        // Module-level matching
        if (str_contains($moduleName, 'math') || str_contains($moduleName, 'algebra')) {
            return $skillTemplates['math'];
        } elseif (str_contains($moduleName, 'science') || str_contains($moduleName, 'biology')) {
            return $skillTemplates['science'];
        } elseif (str_contains($moduleName, 'english') || str_contains($moduleName, 'literature')) {
            return $skillTemplates['english'];
        }

        return $skillTemplates['default'];
    }
}
