# Quick Testing Guide - Dynamic Grade Weights

## 🧪 Manual Testing Steps

### 1. Test the Grade Simulator

#### Access the Simulator:
```
Navigate to: http://your-domain/grade-settings
```

#### Test Scenario 1: Lessons Only
1. ✅ Check "Lessons"
2. ❌ Uncheck all activity types (Quiz, Assignment, Assessment, Exercise)
3. Set lesson score: 90%
4. **Expected Result:**
   - Lesson weight badge shows: 100%
   - Activity weight badge shows: 0%
   - Module score: 90%

#### Test Scenario 2: Activities Only (Quiz)
1. ❌ Uncheck "Lessons"
2. ✅ Check only "Quiz"
3. ❌ Uncheck Assignment, Assessment, Exercise
4. Set quiz score: 85%
5. **Expected Result:**
   - Lesson weight: 0%
   - Activity weight: 100%
   - Quiz weight (within activities): 100%
   - Module score: 85%

#### Test Scenario 3: Lessons + Quiz + Assignment
1. ✅ Check "Lessons"
2. ✅ Check "Quiz" and "Assignment"
3. ❌ Uncheck Assessment and Exercise
4. Set scores:
   - Lesson: 90%
   - Quiz: 80%
   - Assignment: 85%
5. **Expected Result:**
   - Lesson weight: 20% (configured default)
   - Activity weight: 80%
   - Quiz weight: ~66.67% (30/(30+15) × 100)
   - Assignment weight: ~33.33% (15/(30+15) × 100)
   - Activity score: (80 × 0.6667) + (85 × 0.3333) = ~81.67%
   - Module score: (90 × 0.20) + (81.67 × 0.80) = 18 + 65.34 = ~83.34%

#### Test Scenario 4: All Components
1. ✅ Check all components
2. Set varied scores for each
3. **Expected Result:**
   - Weights match configured defaults
   - All calculations use standard weights

### 2. Test with Real Data

#### Find modules with missing components:

```sql
-- Modules with only lessons (no activities)
SELECT m.id, m.description, c.name as course
FROM modules m
JOIN courses c ON m.course_id = c.id
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE module_id = m.id)
AND EXISTS (SELECT 1 FROM lessons WHERE module_id = m.id)
LIMIT 5;

-- Modules with only activities (no lessons)
SELECT m.id, m.description, c.name as course
FROM modules m
JOIN courses c ON m.course_id = c.id
WHERE NOT EXISTS (SELECT 1 FROM lessons WHERE module_id = m.id)
AND EXISTS (SELECT 1 FROM activities WHERE module_id = m.id)
LIMIT 5;
```

#### Test a specific student's grade:

```php
// In tinker or a test route
use App\Services\GradeCalculatorService;
use App\Models\Module;

$service = new GradeCalculatorService();
$module = Module::find(YOUR_MODULE_ID);
$result = $service->calculateModuleGrade(YOUR_STUDENT_ID, $module);

// Check the result
dd([
    'has_lessons' => $result['has_lessons'],
    'has_activities' => $result['has_activities'],
    'lesson_weight_used' => $result['lesson_weight_used'],
    'activity_weight_used' => $result['activity_weight_used'],
    'module_score' => $result['module_score'],
    'activity_types' => $result['activity_types'],
]);
```

### 3. Run Automated Tests

```bash
# Run all dynamic grade weight tests
php artisan test --filter DynamicGradeWeightTest

# Run with verbose output
php artisan test --filter DynamicGradeWeightTest --verbose

# Run a specific test method
php artisan test --filter DynamicGradeWeightTest::it_handles_module_with_only_lessons
```

### 4. API Testing

#### Test via API endpoint (if available):

```bash
# Get student grades for a course
curl -X GET "http://your-domain/api/students/{student_id}/courses/{course_id}/grades" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Check the response structure:
```json
{
  "modules": [
    {
      "module_id": 1,
      "module_score": 88.5,
      "has_lessons": true,
      "has_activities": true,
      "lesson_weight_used": 20,
      "activity_weight_used": 80,
      "activity_types": [
        {
          "type": "Quiz",
          "weight_used": 66.67,
          "configured_weight": 30
        }
      ]
    }
  ]
}
```

## ✅ Verification Checklist

### Backend Implementation:
- [ ] `has_lessons` and `has_activities` flags are returned
- [ ] `lesson_weight_used` and `activity_weight_used` reflect dynamic adjustments
- [ ] Activity types show `weight_used` (normalized) vs `configured_weight`
- [ ] Module scores are calculated correctly with dynamic weights
- [ ] Empty modules return sensible defaults

### Frontend Simulator:
- [ ] Checkboxes work for all components
- [ ] Weight badges update in real-time
- [ ] Disabled inputs have reduced opacity
- [ ] Breakdown shows all calculation steps
- [ ] Final score matches expected calculation

### Edge Cases:
- [ ] Module with no lessons or activities (shouldn't exist but handled gracefully)
- [ ] Module with 100% in all components
- [ ] Module with 0% in all components
- [ ] Module with only one activity of one type
- [ ] Course-specific weights are respected

## 🐛 Common Issues & Solutions

### Issue: Weights don't add to 100%
**Check:**
- Look at the `weight_used` field, not `configured_weight`
- Verify normalization logic in `calculateModuleActivityScore()`

### Issue: Simulator shows different results than backend
**Check:**
- Clear browser cache
- Run `npm run build` to rebuild frontend assets
- Verify computed properties are using the same logic

### Issue: Tests fail
**Check:**
- Database is properly seeded
- Activity types exist in database
- Relationships are properly loaded
- Factory data is valid

## 📊 Expected Test Results

All 4 tests should pass:
```
✓ it handles module with only lessons
✓ it handles module with only activities  
✓ it handles module with only one activity type
✓ it handles module with multiple activity types

Tests:  4 passed
Time:   < 1s
```

## 🎯 Success Criteria

The implementation is successful if:

1. **All automated tests pass** ✅
2. **Simulator matches backend calculations** ✅
3. **Real student grades calculate correctly** ✅
4. **API responses include all new fields** ✅
5. **No regression in existing functionality** ✅

## 📝 Testing Notes

### Performance Testing:
```php
// Test grade calculation performance
$start = microtime(true);
$result = $service->calculateModuleGrade($studentId, $module);
$duration = microtime(true) - $start;

echo "Calculation took: " . ($duration * 1000) . "ms\n";
// Should be < 50ms for most modules
```

### Cache Testing:
```php
// Verify cache invalidation works
use Illuminate\Support\Facades\Cache;

$courseId = 1;
$cacheKey = "course_grade_settings_{$courseId}";

// Check if cached
$cached = Cache::has($cacheKey);

// Save new settings (should clear cache)
// ... save operation ...

// Verify cache was cleared
$stillCached = Cache::has($cacheKey);
// Should be false
```

---

**Last Updated**: January 17, 2025  
**Status**: Ready for Testing  
**Estimated Testing Time**: 30-45 minutes
