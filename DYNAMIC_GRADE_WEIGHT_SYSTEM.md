# Dynamic Grade Weight System

## Overview
The grade calculation system now dynamically adjusts weights based on which components exist in a module. This ensures that grades always total 100% regardless of missing lessons or activity types.

## Problem Solved
Previously, the system used static weights even when some components were missing:

**Example of OLD (Buggy) Behavior:**
```
Module Configuration:
- Configured Weights: Lessons 20%, Activities 80%
- Has: 1 Lesson (score: 90%), 2 Quizzes (avg: 85%)
- Missing: Assignments, Assessments, Exercises

OLD Calculation:
- Lesson: 90% × 20% = 18 points
- Activities: 85% × 80% = 68 points  ❌ WRONG
- Total: 86 points (Only using configured Quiz weight of 30% within activities)
```

**NEW (Correct) Behavior:**
```
Module Configuration:
- Configured Weights: Lessons 20%, Activities 80%
- Has: 1 Lesson (score: 90%), 2 Quizzes (avg: 85%)
- Missing: Assignments, Assessments, Exercises

NEW Calculation:
1. Check what exists: ✓ Lessons, ✓ Activities (Quiz only)
2. Use configured module weights: Lessons 20%, Activities 80%
3. Normalize activity type weights:
   - Quiz configured: 30%
   - Total configured: 30%
   - Quiz normalized: 30/30 × 100 = 100% ✅
4. Calculate:
   - Lesson: 90% × 20% = 18 points
   - Activities: 85% × 100% = 85 points (Quiz gets full activity weight)
   - Activity contribution: 85 × 80% = 68 points
   - Total: 18 + 68 = 86 points ✅ CORRECT
```

## How It Works

### Two-Level Weight Adjustment

#### Level 1: Module Components (Lessons vs Activities)
The system checks if a module has lessons and/or activities:

| Has Lessons | Has Activities | Lesson Weight | Activity Weight |
|------------|----------------|---------------|-----------------|
| ✅ | ✅ | Configured (e.g., 20%) | Configured (e.g., 80%) |
| ✅ | ❌ | **100%** | **0%** |
| ❌ | ✅ | **0%** | **100%** |
| ❌ | ❌ | 50% (fallback) | 50% (fallback) |

#### Level 2: Activity Types (Quiz, Assignment, Assessment, Exercise)
Within activities, the system checks which activity types exist and normalizes their weights:

**Example 1: Only Quizzes exist**
```
Configured Weights:
- Quiz: 30%
- Assignment: 15%
- Assessment: 35%
- Exercise: 20%
Total: 100%

Existing Types: Quiz only
Total Configured for Existing: 30%

Normalized Weights:
- Quiz: 30/30 × 100 = 100% ✅
```

**Example 2: Quiz + Assignment exist**
```
Configured Weights:
- Quiz: 30%
- Assignment: 15%
- Assessment: 35%
- Exercise: 20%

Existing Types: Quiz, Assignment
Total Configured for Existing: 30 + 15 = 45%

Normalized Weights:
- Quiz: 30/45 × 100 = 66.67%
- Assignment: 15/45 × 100 = 33.33%
Total: 100% ✅
```

## Code Implementation

### Backend Service
Located in: `app/Services/GradeCalculatorService.php`

#### Key Methods:

**1. calculateModuleGrade()**
```php
// Check what exists
$hasLessons = $module->lessons()->count() > 0;
$hasActivities = $module->activities()->count() > 0;

// Dynamically adjust weights
if ($hasLessons && $hasActivities) {
    // Use configured weights
    $actualLessonWeight = $configuredLessonWeight;
    $actualActivityWeight = $configuredActivityWeight;
} elseif ($hasLessons && !$hasActivities) {
    // Only lessons: 100%/0%
    $actualLessonWeight = 100;
    $actualActivityWeight = 0;
} elseif (!$hasLessons && $hasActivities) {
    // Only activities: 0%/100%
    $actualLessonWeight = 0;
    $actualActivityWeight = 100;
}
```

**2. calculateModuleActivityScore()**
```php
// Get configured weights for existing activity types
foreach ($activitiesByType as $typeName => $typeActivities) {
    $weight = $this->getActivityTypeWeight($typeName, $module->course_id);
    $configuredWeights[$typeName] = $weight;
    $totalConfiguredWeight += $weight;
}

// Normalize weights to 100%
foreach ($configuredWeights as $typeName => $weight) {
    $normalizedWeights[$typeName] = ($weight / $totalConfiguredWeight) * 100;
}

// Calculate weighted score
foreach ($activitiesByType as $typeName => $typeActivities) {
    $typeScore = $this->calculateActivityTypeScore($userId, $typeActivities, $module->id);
    $normalizedWeight = $normalizedWeights[$typeName];
    $weightedScore += ($typeScore['average_score'] * ($normalizedWeight / 100));
}
```

### Frontend Simulator
Located in: `resources/js/Pages/Admin/GradeSettings.vue`

The grade simulator now includes:
- Checkboxes to toggle which components exist
- Real-time weight normalization display
- Dynamic grade calculation matching backend logic

**Features:**
1. **Component Toggles**: Check/uncheck lessons and activity types
2. **Live Weight Display**: Shows normalized weights next to each component
3. **Breakdown Display**: Shows lesson contribution, activity score, and activity contribution
4. **Real-time Updates**: Recalculates immediately when toggling components

## API Response Structure

### Module Grade Response
```json
{
  "module_id": 1,
  "module_title": "Introduction to PHP",
  "module_score": 88.5,
  "has_lessons": true,
  "has_activities": true,
  "lesson_score": 100,
  "lesson_weight_used": 20,
  "lesson_contribution": 20,
  "activity_score": 85.63,
  "activity_weight_used": 80,
  "activity_contribution": 68.5,
  "activity_types": [
    {
      "type": "Quiz",
      "type_score": 90,
      "configured_weight": 30,
      "weight_used": 66.67,
      "completed_count": 2,
      "total_count": 2
    },
    {
      "type": "Assignment",
      "type_score": 80,
      "configured_weight": 15,
      "weight_used": 33.33,
      "completed_count": 1,
      "total_count": 1
    }
  ]
}
```

### Key Fields:
- `has_lessons`: Boolean indicating if module has lessons
- `has_activities`: Boolean indicating if module has activities
- `lesson_weight_used`: Actual weight applied to lessons (may differ from configured)
- `activity_weight_used`: Actual weight applied to activities (may differ from configured)
- `weight_used`: Normalized weight for each activity type (within activity_types array)
- `configured_weight`: Original configured weight for reference

## Testing

### Test File
`tests/Feature/DynamicGradeWeightTest.php`

### Test Scenarios:
1. **Module with only lessons**
   - Verifies lessons get 100% weight
   - Activities get 0% weight

2. **Module with only activities**
   - Verifies activities get 100% weight
   - Lessons get 0% weight

3. **Module with only one activity type**
   - Verifies activity type gets 100% of activity weight
   - Other types are excluded

4. **Module with multiple activity types**
   - Verifies weights are normalized to 100%
   - All existing types contribute proportionally

### Running Tests
```bash
php artisan test --filter DynamicGradeWeightTest
```

## Configuration

### Global Settings
Set default weights in the admin panel:
1. Navigate to `/grade-settings`
2. Configure "Module Components" (Lessons vs Activities)
3. Configure "Activity Types" (Quiz, Assignment, etc.)
4. Click "Save" for each section

### Course-Specific Settings
Override global settings for specific courses:
1. Navigate to `/grade-settings`
2. Search and select a course
3. Modify weights as needed
4. Click "Save" to create course-specific settings
5. Click "Revert to Global" to remove overrides

## Grade Simulator

### Using the Simulator
1. **Select Components**: Check/uncheck which components exist
2. **Enter Scores**: Input hypothetical scores for each component
3. **View Results**: See real-time calculation with:
   - Normalized weights for each component
   - Lesson contribution
   - Activity score and contribution
   - Final module score
   - Letter grade

### Example Scenarios to Test:

**Scenario 1: Only Lessons**
- Uncheck all activity types
- Check lessons
- Enter lesson score: 90%
- Result: Module score = 90% (lessons at 100% weight)

**Scenario 2: Only Quizzes**
- Uncheck lessons
- Check only "Quiz"
- Enter quiz score: 85%
- Result: Quiz gets 100% of activity weight, activities get 100% of module weight
- Module score = 85%

**Scenario 3: Balanced Module**
- Check lessons and all activity types
- Enter varied scores
- See how configured weights apply when all components exist

## Best Practices

### 1. Weight Configuration
- **Module Components**: Typically 20% lessons, 80% activities for skills-focused courses
- **Activity Types**: Adjust based on course emphasis:
  - Assessment-heavy: Higher Assessment weight
  - Practice-focused: Higher Exercise weight

### 2. Course Design
- Ensure consistent component availability across modules when possible
- If modules have different structures, use course-specific grade settings
- Document why certain modules lack specific components

### 3. Monitoring
- Review grade distributions regularly
- Check if dynamic adjustments are working as expected
- Use simulator to verify calculation logic

## Troubleshooting

### Issue: Grades seem incorrect
**Check:**
1. Module has at least one component (lessons OR activities)
2. Activities are properly categorized by type
3. Student completions are recorded correctly
4. Course-specific settings aren't overriding unexpectedly

### Issue: Weights don't add to 100%
**Solution:**
- The system auto-normalizes weights
- Verify in API response: check `weight_used` fields
- Use simulator to confirm behavior

### Issue: Missing components not excluded
**Verify:**
1. Activities have proper `activity_type_id`
2. Lessons are properly linked to module
3. Database relationships are intact
4. Cache is cleared (grade settings are cached for 1 hour)

## Technical Notes

### Performance
- Grade settings are cached per course for 1 hour
- Cache is automatically invalidated on setting updates
- Dynamic weight calculations add minimal overhead

### Database
- Settings stored in `grade_settings` (global) and `course_grade_settings` (per-course)
- No changes to student activity or lesson completion tables
- Fully backward compatible

### Caching Strategy
```php
// Cache key format
$cacheKey = "course_grade_settings_{$courseId}";

// Automatic invalidation
Cache::forget($cacheKey); // On save or delete
```

## Future Enhancements

Potential improvements:
1. **Module-specific weights**: Override at module level
2. **Time-based adjustments**: Different weights for different periods
3. **Student group settings**: Custom weights per student cohort
4. **Weight history**: Track weight changes over time
5. **Analytics dashboard**: Visualize weight impact on grades

---

**Last Updated**: January 2025
**Version**: 1.0
**Author**: Development Team
