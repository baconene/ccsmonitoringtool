# Student Assessment System - Quick Start Guide

## Installation & Setup

### Step 1: Run Database Migrations

```bash
php artisan migrate
```

This creates three new tables:
- `skills` - Competency definitions
- `student_skill_assessments` - Student progress tracking  
- `skill_activities` - Skill-activity relationships

### Step 2: Seed Initial Data

```bash
# Add skills to existing modules
php artisan db:seed --class=SkillSeeder

# Calculate assessments for enrolled students
php artisan db:seed --class=StudentSkillAssessmentSeeder
```

### Step 3: Verify Installation

Check that data was seeded correctly:

```bash
# In Laravel Tinker
php artisan tinker
> App\Models\Skill::count()
> App\Models\StudentSkillAssessment::count()
```

## Testing the System

### Via API

Use a tool like Postman or curl:

```bash
# Get your authenticated student assessment
curl http://localhost:8000/api/student/assessment \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN" \
  -H "Content-Type: application/json"

# Get skill assessments
curl http://localhost:8000/api/student/skills/assessments \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN"

# Get strengths
curl http://localhost:8000/api/student/strengths \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN"

# Get weaknesses  
curl http://localhost:8000/api/student/weaknesses \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN"

# Get radar chart data
curl http://localhost:8000/api/student/assessment/radar \
  -H "Authorization: Bearer YOUR_SANCTUM_TOKEN"
```

### Via Frontend

1. Add route to your router:
   ```typescript
   // resources/js/routes/index.ts
   {
     path: '/assessment',
     component: () => import('@/pages/Student/MyAssessment.vue'),
     meta: { requiresAuth: true }
   }
   ```

2. Add navigation link:
   ```vue
   <nav>
     <Link href="/assessment">My Assessment</Link>
   </nav>
   ```

3. Visit: `http://localhost:8000/assessment`

### Running Tests

```bash
# Run all assessment tests
php artisan test tests/Unit/Services/StudentAssessmentServiceTest.php

# Run specific test
php artisan test tests/Unit/Services/StudentAssessmentServiceTest.php::StudentAssessmentServiceTest::test_normalized_score_calculation

# Run with verbose output
php artisan test tests/Unit/Services/StudentAssessmentServiceTest.php -v

# Run with coverage
php artisan test tests/Unit/Services/StudentAssessmentServiceTest.php --coverage
```

## Dependencies

The system uses these external packages (likely already in your project):

```json
{
  "require": {
    "laravel/framework": "^12.0",
    "laravel/sanctum": "^4.2"
  },
  "require-dev": {
    "phpunit/phpunit": "^11.5"
  }
}
```

For frontend, you need Chart.js installed:

```bash
npm install chart.js
```

If not already installed, add to your Vite config:

```typescript
// vite.config.ts
export default defineConfig({
  // ... other config
  // chart.js should be automatically available
})
```

## Data Model Quick Reference

### Skills
```php
Skill::create([
  'module_id' => 1,
  'name' => 'Critical Thinking',
  'difficulty_level' => 'advanced',
  'weight' => 20,
  'competency_threshold' => 75,
  'bloom_level' => 'analyze'
])
```

### Link Skill to Activity
```php
$skill->activities()->attach($activity->id, ['weight' => 1.0]);
```

### Get Student Assessment
```php
$service = app(\App\Services\StudentAssessmentService::class);
$assessment = $service->calculateStudentAssessment($student);
```

## Common Tasks

### Create Skills for a Module

```php
// Option 1: Manual
$module = Module::find(1);
$skill = Skill::create([
  'module_id' => $module->id,
  'name' => 'Advanced Problem Solving',
  'difficulty_level' => 'advanced',
  'weight' => 25,
  'competency_threshold' => 80,
  'bloom_level' => 'create'
]);

// Option 2: Seed additional skills
php artisan db:seed --class=SkillSeeder
```

### Link Activities to Skills

```php
$skill = Skill::find(1);
$activity = Activity::find(5);

// Single link with weight
$skill->activities()->attach($activity->id, ['weight' => 1.0]);

// Bulk attach
$skill->activities()->attach([
  5 => ['weight' => 1.0],
  6 => ['weight' => 0.8],
  7 => ['weight' => 1.0]
]);
```

### Recalculate Assessments

```php
// For single student
$service = app(\App\Services\StudentAssessmentService::class);
$assessment = $service->calculateStudentAssessment($student);

// For course students
$course = Course::find(1);
foreach ($course->students as $student) {
  $service->calculateStudentAssessment($student);
}
```

### Compare Students

```bash
curl -X POST http://localhost:8000/api/admin/assessment/compare \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"student_ids": [1, 2, 3, 4, 5]}'
```

## Debugging

### Enable Query Logging

```php
// In a feature test or artisan command
\Illuminate\Support\Facades\DB::enableQueryLog();
// ... run assessment code ...
dd(\Illuminate\Support\Facades\DB::getQueryLog());
```

### Check Calculation Steps

```php
// In StudentAssessmentService, add logging
Log::info('Calculating assessment', [
  'student_id' => $student->id,
  'skill_count' => $skills->count(),
  'activity_scores' => $activityScores
]);
```

### Verify Seeded Data

```bash
php artisan tinker
> use App\Models\Student, App\Models\Skill, App\Models\Module
> Student::count()
> Module::count()
> Skill::count()
> $skill = Skill::first(); $skill->activities()->count()
```

## Performance Tips

### Cache Assessment Results

```php
// In StudentAssessmentService
$cacheKey = "student_assessment_{$student->id}";
$assessment = cache()->remember($cacheKey, 3600, function () use ($student) {
  return $this->calculateStudentAssessment($student);
});
```

### Eager Load Relationships

```php
// Reduce query count
$students = Student::with('courses.modules.skills', 'skillAssessments.skill')
  ->get();
```

### Index Important Columns

```sql
-- Already in migration, but verify:
ALTER TABLE student_skill_assessments 
ADD INDEX idx_student_skill (student_id, skill_id);

ALTER TABLE skills ADD INDEX idx_module (module_id);
```

## Troubleshooting

### "Student not found" Error

```bash
# Verify student enrollment
php artisan tinker
> $student = Student::find(1)
> $student->courses()->count()  // Should be > 0
```

### "No module performance data available"

```bash
# Check module-skill relationships
> Module::with('skills')->find(1)->skills()->count()
```

### Radar chart not showing

```javascript
// In browser console
console.log(assessment.radar_chart.labels.length)  // Should > 0
console.log(assessment.radar_chart.datasets)  // Check format
```

### Late penalty not applied

```php
// In StudentAssessmentService
$daysLate = $activity->due_date->diffInDays($studentActivity->completed_at);
// Verify this value is correct
```

## Extending the System

### Add Custom Adjustment Factor

```php
// In StudentAssessmentService::calculateOrUpdateSkillAssessment()
$customFactor = $this->calculateCustomAdjustment($student, $skill);
$finalScore += $customFactor;
```

### Custom Recommendation Engine

```php
// Create new class
class SkillRecommendationEngine {
  public function getRecommendations(StudentSkillAssessment $assessment): array {
    // Custom logic
  }
}
```

### Add More Visualization Charts

```vue
<!-- New component: ComparisonChart.vue -->
<template>
  <canvas ref="canvas"></canvas>
</template>
<script setup>
// Similar to RadarChart.vue but with different chart type
</script>
```

## Support Resources

- Main Documentation: [STUDENT_ASSESSMENT_SYSTEM.md](./STUDENT_ASSESSMENT_SYSTEM.md)
- API Error Responses: Check `AssessmentController` error handling
- Database Structure: Review migrations in `database/migrations/`
- Service Logic: `app/Services/StudentAssessmentService.php`

---

**Quick Checklist:**
- [ ] Migrations run successfully
- [ ] Seeds executed without errors
- [ ] Can fetch assessment via API
- [ ] MyAssessment page loads
- [ ] Radar chart displays
- [ ] Tests pass
- [ ] No database errors in logs
