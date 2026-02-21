<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Skill;
use App\Models\StudentSkillAssessment;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use Illuminate\Support\Collection;

class StudentAssessmentService
{
    protected const DEFAULT_LATE_PENALTY_PER_DAY = 2.0; // 2% penalty per day
    protected const MASTERY_EXCEEDS_BONUS = 15.0; // 15% above threshold

    /**
     * Calculate overall assessment for a student across all enrolled courses
     */
    public function calculateStudentAssessment(Student $student): array
    {
        $enrolledCourses = $student->courses()->get();

        if ($enrolledCourses->isEmpty()) {
            return $this->getEmptyAssessmentStructure($student);
        }

        $courseAssessments = [];
        $allCourseScores = [];

        foreach ($enrolledCourses as $course) {
            $courseAssessment = $this->calculateCourseAssessment($student, $course);
            $courseAssessments[] = $courseAssessment;
            $allCourseScores[] = $courseAssessment['score'];
        }

        // Calculate overall score (weighted average of courses)
        $overallScore = !empty($allCourseScores)
            ? array_sum($allCourseScores) / count($allCourseScores)
            : 0;

        // Determine readiness level
        $readinessLevel = $this->determineReadinessLevel($overallScore);

        // Get strengths and weaknesses based on skill assessments
        $allSkillAssessments = $student->skillAssessments()
            ->with('skill')
            ->get();

        $strengths = $this->identifyStrengths($allSkillAssessments);
        $weaknesses = $this->identifyWeaknesses($allSkillAssessments);

        // Build radar chart data using skill assessments
        $radarChartData = $this->buildRadarChartData($allSkillAssessments);

        return [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'overall_score' => round($overallScore, 2),
            'readiness_level' => $readinessLevel,
            'courses' => $courseAssessments,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'radar_chart' => $radarChartData,
            'assessment_date' => now()->toIso8601String(),
        ];
    }

    /**
     * Calculate assessment for a specific course
     */
    public function calculateCourseAssessment($student, $course): array
    {
        $modules = $course->modules()->get();

        if ($modules->isEmpty()) {
            return [
                'course_id' => $course->id,
                'course_name' => $course->title,
                'score' => 0,
                'modules' => [],
            ];
        }

        $moduleAssessments = [];
        $moduleScores = [];

        foreach ($modules as $module) {
            $moduleAssessment = $this->calculateModuleAssessment($student, $module);
            $moduleAssessments[] = $moduleAssessment;
            
            // Weight the module score
            $weight = ($module->module_percentage ?? (100 / $modules->count())) / 100;
            $moduleScores[] = $moduleAssessment['score'] * $weight;
        }

        // Calculate course score
        $courseScore = !empty($moduleScores)
            ? array_sum($moduleScores)
            : 0;

        return [
            'course_id' => $course->id,
            'course_name' => $course->title,
            'score' => round($courseScore, 2),
            'modules' => $moduleAssessments,
        ];
    }

    /**
     * Calculate assessment for a specific module
     */
    public function calculateModuleAssessment($student, $module): array
    {
        $skills = $module->skills()->get();

        if ($skills->isEmpty()) {
            return [
                'module_id' => $module->id,
                'module_name' => $module->title,
                'score' => 0,
                'skills' => [],
            ];
        }

        $skillAssessments = [];
        $skillScores = [];

        foreach ($skills as $skill) {
            $assessment = $this->calculateOrUpdateSkillAssessment($student, $skill);
            
            $skillAssessments[] = [
                'skill_id' => $skill->id,
                'skill_name' => $skill->name,
                'score' => $assessment['final_score'],
                'mastery_level' => $assessment['mastery_level'],
            ];

            // Weight the skill score
            $weight = ($skill->weight ?? 1.0) / 100;
            $skillScores[] = $assessment['final_score'] * $weight;
        }

        // Calculate module score
        $moduleScore = !empty($skillScores)
            ? array_sum($skillScores)
            : 0;

        return [
            'module_id' => $module->id,
            'module_name' => $module->title,
            'score' => round($moduleScore, 2),
            'skills' => $skillAssessments,
        ];
    }

    /**
     * Calculate or update skill assessment for a student
     */
    public function calculateOrUpdateSkillAssessment(Student $student, Skill $skill): array
    {
        // Get all activities for this skill
        $activities = $skill->activities()->get();

        if ($activities->isEmpty()) {
            return $this->createEmptySkillAssessment($skill);
        }

        // Get student's performance on activities related to this skill
        $activityScores = [];
        $totalAttempts = 0;
        $totalDaysLate = 0;

        foreach ($activities as $activity) {
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $activity->id)
                ->first();

            if ($studentActivity) {
                // Get the score from the appropriate progress table
                $score = $this->extractActivityScore($studentActivity);
                
                if ($score !== null) {
                    // Get activity weight for this skill
                    $activityWeight = $activity->skills()
                        ->where('skill_id', $skill->id)
                        ->first()
                        ?->pivot->weight ?? 1.0;

                    // Store with weight
                    $activityScores[] = [
                        'score' => $score,
                        'weight' => $activityWeight,
                        'attempts' => $studentActivity->attempt_count ?? 1,
                        'days_late' => $this->calculateDaysLate($studentActivity),
                    ];

                    $totalAttempts += $studentActivity->attempt_count ?? 1;
                    $totalDaysLate += $this->calculateDaysLate($studentActivity);
                }
            }
        }

        if (empty($activityScores)) {
            return $this->createEmptySkillAssessment($skill);
        }

        // Normalize and calculate weighted skill score
        $normalizedScore = $this->calculateNormalizedScore($activityScores);
        $feedbackBonus = 0; // Can be extended to support feedback scores
        $peerReviewBonus = 0; // Can be extended to support peer review
        $improvementFactor = $this->calculateImprovementFactor($totalAttempts);
        $latePenalty = $this->calculateLatePenalty($totalDaysLate);

        // Final score calculation
        $finalScore = $normalizedScore
            + ($feedbackBonus * 0.1) // 10% weight for feedback
            + ($peerReviewBonus * 0.1) // 10% weight for peer review
            + ($improvementFactor - 1) * 10 // Improvement bonus
            - $latePenalty; // Apply late penalty

        $finalScore = max(0, min(100, $finalScore)); // Clamp between 0-100

        // Determine mastery level
        $threshold = $skill->competency_threshold ?? 70;
        $mastery = $this->determineMasteryLevel($finalScore, $threshold);

        // Calculate consistency
        $consistencyScore = $this->calculateConsistencyScore($activityScores);

        // Create or update the assessment record
        $assessment = StudentSkillAssessment::updateOrCreate(
            [
                'student_id' => $student->id,
                'skill_id' => $skill->id,
            ],
            [
                'normalized_score' => round($normalizedScore, 2),
                'feedback_score' => $feedbackBonus,
                'peer_review_score' => $peerReviewBonus,
                'attempt_count' => $totalAttempts,
                'improvement_factor' => round($improvementFactor, 2),
                'days_late' => $totalDaysLate,
                'final_score' => round($finalScore, 2),
                'mastery_level' => $mastery,
                'consistency_score' => round($consistencyScore, 2),
                'assessment_metadata' => [
                    'activity_count' => count($activityScores),
                    'evaluation_date' => now()->toDateTimeString(),
                ],
            ]
        );

        return [
            'skill_id' => $skill->id,
            'normalized_score' => $normalizedScore,
            'final_score' => $assessment->final_score,
            'mastery_level' => $mastery,
            'consistency_score' => $consistencyScore,
        ];
    }

    /**
     * Calculate normalized score from activity scores
     */
    protected function calculateNormalizedScore(array $activityScores): float
    {
        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($activityScores as $item) {
            // Normalize to 0-100
            $score = min(100, max(0, $item['score']));
            $weightedSum += $score * $item['weight'];
            $totalWeight += $item['weight'];
        }

        return $totalWeight > 0 ? ($weightedSum / $totalWeight) : 0;
    }

    /**
     * Calculate improvement factor based on attempts
     */
    protected function calculateImprovementFactor(int $attemptCount): float
    {
        // First attempt = 1.0, each subsequent attempt adds progressive bonus
        if ($attemptCount <= 1) {
            return 1.0;
        }

        // Diminishing returns: 2nd attempt = 1.05, 3rd = 1.07, etc.
        return 1.0 + (log($attemptCount) * 0.035);
    }

    /**
     * Calculate late penalty
     */
    protected function calculateLatePenalty(int $daysLate): float
    {
        return min(30, $daysLate * self::DEFAULT_LATE_PENALTY_PER_DAY);
    }

    /**
     * Determine mastery level based on score and threshold
     */
    protected function determineMasteryLevel(float $score, float $threshold): string
    {
        if ($score >= ($threshold + self::MASTERY_EXCEEDS_BONUS)) {
            return 'exceeds';
        } elseif ($score >= $threshold) {
            return 'met';
        } else {
            return 'not_met';
        }
    }

    /**
     * Calculate consistency score (coefficient of variation)
     */
    protected function calculateConsistencyScore(array $activityScores): float
    {
        if (count($activityScores) < 2) {
            return 100; // Perfect consistency if only one item
        }

        $scores = array_column($activityScores, 'score');
        $mean = array_sum($scores) / count($scores);
        $variance = array_sum(array_map(
            fn($score) => pow($score - $mean, 2),
            $scores
        )) / count($scores);
        $stdDev = sqrt($variance);

        // Convert to 0-100 scale (lower std dev = higher consistency)
        return max(0, 100 - ($stdDev * 2));
    }

    /**
     * Identify strength areas (top 20% skills), ensuring distinct skills
     * and averaging scores when the same skill appears multiple times.
     */
    protected function identifyStrengths(Collection $assessments, int $topPercentile = 20): array
    {
        if ($assessments->isEmpty()) {
            return [];
        }

        // Group by skill name to ensure distinct items and average their scores
        $aggregated = $assessments
            ->groupBy(fn ($assessment) => $assessment->skill->name)
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'skill_name' => $first->skill->name,
                    'score' => round($group->avg('final_score'), 2),
                    'difficulty' => $first->skill->difficulty_level,
                    'tags' => $first->skill->tags ?? [],
                ];
            })
            ->values();

        // Sort by score descending and take the top percentile
        $sortedByScore = $aggregated->sortByDesc('score')->values();
        $cutOffIndex = max(1, (int) ceil($sortedByScore->count() * ($topPercentile / 100)));

        return $sortedByScore
            ->slice(0, $cutOffIndex)
            ->values()
            ->toArray();
    }

    /**
     * Identify weakness areas (below competency threshold)
     */
    protected function identifyWeaknesses(Collection $assessments): array
    {
        return $assessments
            ->filter(fn($assessment) => !$assessment->isMastered())
            ->sortBy('final_score')
            ->map(fn($assessment) => [
                'skill_name' => $assessment->skill->name,
                'score' => $assessment->final_score,
                'threshold' => $assessment->skill->competency_threshold,
                'gap' => round($assessment->skill->competency_threshold - $assessment->final_score, 2),
                'difficulty' => $assessment->skill->difficulty_level,
                'recommendations' => $this->generateRecommendations($assessment),
            ])
            ->toArray();
    }

    /**
     * Generate improvement recommendations based on skill assessment
     */
    protected function generateRecommendations(StudentSkillAssessment $assessment): array
    {
        $recommendations = [];

        if ($assessment->final_score < 50) {
            $recommendations[] = 'Focus on fundamentals - Consider revisiting introductory materials';
        }

        if ($assessment->attempt_count > 3 && $assessment->improvement_factor < 1.05) {
            $recommendations[] = 'Multiple attempts without improvement - Try a different learning approach';
        }

        if ($assessment->consistency_score < 50) {
            $recommendations[] = 'Inconsistent performance - Work on building stable understanding';
        }

        if ($assessment->skill->difficulty_level === 'advanced' && $assessment->final_score < 60) {
            $recommendations[] = 'Consider strengthening prerequisite skills first';
        }

        return $recommendations;
    }

    /**
     * Build radar chart data for visualization based on skill assessments.
     *
     * Each axis represents a distinct skill with its averaged score.
     */
    protected function buildRadarChartData(Collection $assessments): array
    {
        if ($assessments->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Aggregate by skill name to get distinct skills and average scores
        $aggregated = $assessments
            ->groupBy(fn ($assessment) => $assessment->skill->name)
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'label' => $first->skill->name,
                    'score' => round($group->avg('final_score'), 2),
                ];
            })
            ->values();

        // Use top 8 skills by score for readability
        $topSkills = $aggregated->sortByDesc('score')->take(8)->values();

        $labels = $topSkills->pluck('label')->all();
        $data = $topSkills->pluck('score')->all();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Skill Performance',
                    'data' => $data,
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    /**
     * Determine readiness level
     */
    protected function determineReadinessLevel(float $score): string
    {
        if ($score >= 85) {
            return 'Advanced';
        } elseif ($score >= 70) {
            return 'Proficient';
        } elseif ($score >= 50) {
            return 'Developing';
        } else {
            return 'Not Ready';
        }
    }

    /**
     * Extract score from student activity based on type.
     *
     * Primary source is the unified student_activity_progress table, which
     * holds percentage_score for all activity types. This ensures that
     * completed quizzes/assignments/projects/assessments contribute to the
     * competency calculation even if legacy per-type tables were dropped.
     */
    protected function extractActivityScore(StudentActivity $studentActivity): ?float
    {
        // 1) Unified progress record (authoritative source)
        $progress = StudentActivityProgress::where('student_id', $studentActivity->student_id)
            ->where('activity_id', $studentActivity->activity_id)
            ->first();

        if ($progress) {
            if (!is_null($progress->percentage_score)) {
                return (float) $progress->percentage_score;
            }

            if (!is_null($progress->score) && !is_null($progress->max_score) && $progress->max_score > 0) {
                return (float) (($progress->score / $progress->max_score) * 100);
            }
        }

        // 2) Fallback: percentage_score directly on StudentActivity (if set)
        if (!is_null($studentActivity->percentage_score)) {
            return (float) $studentActivity->percentage_score;
        }

        // 3) Legacy fallbacks (guarded; legacy tables may no longer exist)
        try {
            if ($studentActivity->assignmentProgress) {
                return $studentActivity->assignmentProgress->score;
            }
        } catch (\Throwable $e) {
            // Ignore if backing table no longer exists
        }

        try {
            if ($studentActivity->projectProgress) {
                return $studentActivity->projectProgress->score;
            }
        } catch (\Throwable $e) {
        }

        try {
            if ($studentActivity->assessmentProgress) {
                return $studentActivity->assessmentProgress->score;
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Calculate days late for an activity
     */
    protected function calculateDaysLate(StudentActivity $studentActivity): int
    {
        $activity = $studentActivity->activity;
        
        if (!$activity->due_date || !$studentActivity->completed_at) {
            return 0;
        }

        $dueDate = $activity->due_date;
        $completedDate = $studentActivity->completed_at;

        if ($completedDate <= $dueDate) {
            return 0;
        }

        return $completedDate->diffInDays($dueDate);
    }

    /**
     * Get empty assessment structure
     */
    protected function getEmptyAssessmentStructure(Student $student): array
    {
        return [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'overall_score' => 0,
            'readiness_level' => 'Not Ready',
            'courses' => [],
            'strengths' => [],
            'weaknesses' => [],
            'radar_chart' => [
                'labels' => [],
                'datasets' => [],
            ],
            'assessment_date' => now()->toIso8601String(),
        ];
    }

    /**
     * Create empty skill assessment
     */
    protected function createEmptySkillAssessment(Skill $skill): array
    {
        return [
            'skill_id' => $skill->id,
            'normalized_score' => 0,
            'final_score' => 0,
            'mastery_level' => 'not_met',
            'consistency_score' => 0,
        ];
    }
}
