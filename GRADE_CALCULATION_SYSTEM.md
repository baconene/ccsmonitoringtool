# Grade Calculation System Documentation

## Overview

The Learning Management System uses a sophisticated, hierarchical grade calculation system that dynamically adapts to course content and configuration. Grades are composed of **Lessons** and **Activities**, with activities further broken down by **Activity Type** (Quiz, Assignment, Assessment, Exercise, etc.).

## Grade Calculation Hierarchy

```
Course Grade (100%)
│
├─ Module 1 Grade
│  ├─ Lessons Component (configurable %, default 20%)
│  └─ Activities Component (configurable %, default 80%)
│     ├─ Quiz (configurable weight)
│     ├─ Assignment (configurable weight)
│     ├─ Assessment (configurable weight)
│     └─ Exercise (configurable weight)
│
├─ Module 2 Grade
│  └─ ... (same structure)
│
└─ Module N Grade
   └─ ... (same structure)
```

## Core Components

### 1. Module Components
Modules are graded based on two major components:
- **Lessons**: Completion-based grading (default weight: 20%)
- **Activities**: Performance-based grading (default weight: 80%)

**Weight Adjustment**: If a module has only lessons or only activities, the system automatically adjusts weights to 100% for the existing component.

### 2. Activity Types
Activities are dynamically categorized by type and weighted accordingly:
- **Quiz**: Interactive assessments with immediate feedback
- **Assignment**: Submitted work requiring grading
- **Assessment**: Comprehensive skill evaluations
- **Exercise**: Practice activities

**Dynamic Weight Normalization**: If a module contains only some activity types (e.g., only Quizzes and Assignments), the system automatically normalizes their weights to total 100% proportionally.

## Database Structure

### Tables

#### 1. `activity_types`
Stores all available activity types in the system.
```sql
- id
- name (e.g., 'Quiz', 'Assignment', 'Assessment', 'Exercise')
- description
- model (polymorphic class reference)
```

#### 2. `grade_settings` (Global Settings)
Default grade weights for all courses.
```sql
- id
- setting_type ('module_component' | 'activity_type')
- setting_key (e.g., 'lessons', 'activities', 'Quiz', 'Assignment')
- activity_type_id (FK to activity_types, nullable)
- display_name
- weight_percentage (0-100)
- description
- is_active
```

#### 3. `course_grade_settings` (Course-Specific Settings)
Override global settings for specific courses.
```sql
- id
- course_id (FK to courses)
- setting_type ('module_component' | 'activity_type')
- setting_key (e.g., 'lessons', 'activities', 'Quiz', 'Assignment')
- activity_type_id (FK to activity_types, nullable)
- display_name
- weight_percentage (0-100)
- is_active
```

## Dynamic Activity Type Integration

### Automatic Synchronization
The system automatically syncs grade settings with the `activity_types` table:

1. **On Migration**: Populates `activity_type_id` for existing records
2. **On New Activity Type Creation**: Creates default grade settings (weight: 0%)
3. **On Grade Calculation**: Ensures all activity types have corresponding settings

### Model Relationships

**GradeSetting Model**:
```php
public function activityType()
{
    return $this->belongsTo(ActivityType::class, 'activity_type_id');
}
```

**CourseGradeSetting Model**:
```php
public function activityType()
{
    return $this->belongsTo(ActivityType::class, 'activity_type_id');
}
```

## Grade Calculation Logic

### Module Grade Formula

```
Module Score = (Lesson Score × Lesson Weight) + (Activity Score × Activity Weight)
```

**Example** (with default weights):
```
Lesson Score: 90%
Activity Score: 85%
Lesson Weight: 20%
Activity Weight: 80%

Module Score = (90 × 0.20) + (85 × 0.80)
             = 18 + 68
             = 86%
```

### Activity Score Calculation

Activities are weighted by type and normalized:

```
Activity Score = Σ(Activity Type Score × Normalized Weight)
```

**Example**:
```
Module contains:
- 2 Quizzes (avg score: 90%, configured weight: 30%)
- 1 Assignment (score: 85%, configured weight: 15%)
- (No Assessments or Exercises)

Total Configured Weight = 30 + 15 = 45%

Normalized Weights:
- Quiz: (30 / 45) × 100 = 66.67%
- Assignment: (15 / 45) × 100 = 33.33%

Activity Score = (90 × 0.6667) + (85 × 0.3333)
               = 60 + 28.33
               = 88.33%
```

### Lesson Score Calculation

Lesson scores are completion-based:
```
Lesson Score = (Completed Lessons / Total Lessons) × 100%
```

## API & Service Layer

### GradeCalculatorService

**Key Methods**:

1. **`calculateStudentCourseGrades($userId, $courseId)`**
   - Returns comprehensive grade data for a student in a course
   - Includes module breakdown, activity details, and overall grade

2. **`calculateModuleGrade($userId, $module)`**
   - Calculates grade for a specific module
   - Automatically adjusts weights based on content availability

3. **`getActivityTypeWeights($courseId)`**
   - Fetches activity type weights (course-specific or global)
   - Automatically syncs with `activity_types` table

### Model Methods

**GradeSetting**:
- `syncActivityTypeSettings()`: Syncs settings with activity_types table
- `getActivityTypeWeightsDynamic()`: Gets weights with auto-sync
- `validateActivityTypeWeights($weights)`: Ensures weights total 100%

**CourseGradeSetting**:
- `syncActivityTypeSettings($courseId)`: Syncs course settings
- `getActivityTypeWeightsDynamic($courseId)`: Gets course-specific weights
- `copyGlobalSettingsToCourse($courseId, $userId)`: Copies global defaults

## Student Grade Report

### Display Components

1. **Overall Course Grade**: Weighted average of all module grades
2. **Module Performance**: Individual module scores with breakdowns
3. **Lesson Component**: Score and weight contribution
4. **Activity Component**: Score and weight contribution
5. **Activity Type Breakdown**: Per-type scores, weights, and completion status
6. **Activity Details**: Individual activity scores and status

### UI Features

- **Collapsible Calculation Details**: Students can view the complete calculation formula
- **Dynamic Weight Display**: Shows adjusted weights when content is missing
- **Activity Type Weights**: Visual breakdown of how each activity type contributes
- **Progress Bars**: Visual representation of scores per component
- **Export Options**: PDF and CSV export of grade reports

## Configuration & Administration

### Setting Up Grade Weights

**Global Settings** (applies to all courses):
```php
// Navigate to: Admin > Grade Settings
// Configure module component weights (must total 100%)
Lessons: 20%
Activities: 80%

// Configure activity type weights (must total 100%)
Quiz: 30%
Assignment: 15%
Assessment: 35%
Exercise: 20%
```

**Course-Specific Settings** (overrides global):
```php
// Navigate to: Course > Settings > Grade Configuration
// Click "Customize Grade Settings" to override global defaults
```

### Adding New Activity Types

When a new activity type is added to the system:

1. Add record to `activity_types` table
2. System automatically creates grade settings with 0% weight
3. Administrator must configure appropriate weight percentage
4. Weight redistribution across other types may be needed to maintain 100% total

### Migration Commands

```bash
# Run the migration to add activity_type_id
php artisan migrate

# Sync all grade settings with activity_types
# (This is done automatically, but can be triggered manually)
php artisan tinker
GradeSetting::syncActivityTypeSettings();
CourseGradeSetting::syncActivityTypeSettings($courseId);
```

## Best Practices

### Weight Distribution

1. **Module Components**: 
   - Lessons: 20-30% (completion-based)
   - Activities: 70-80% (performance-based)
   - Total: Must equal 100%

2. **Activity Types**:
   - Distribute based on course objectives
   - Consider difficulty and time investment
   - Total: Must equal 100%

### Dynamic Adjustment

The system handles edge cases gracefully:
- ✅ Module with only lessons → Lessons = 100%
- ✅ Module with only activities → Activities = 100%
- ✅ Module with only 2 activity types → Weights normalized proportionally
- ✅ New activity type added → Default 0%, requires admin configuration

### Performance Optimization

- Grade settings are cached (3600 seconds)
- Cache automatically cleared on settings update
- Module grades calculated on-demand
- Course-level grades use weighted averages

## Troubleshooting

### Grades Not Updating

1. Check cache: Settings changes may take up to 1 hour to reflect
2. Clear cache manually: `php artisan cache:clear`
3. Verify activity completion status in `student_activity_progress` table

### Weight Validation Errors

1. Ensure module component weights (lessons + activities) = 100%
2. Ensure activity type weights sum to 100%
3. Use validation methods:
   ```php
   GradeSetting::validateModuleComponentWeights(['lessons' => 20, 'activities' => 80]);
   GradeSetting::validateActivityTypeWeights(['Quiz' => 25, 'Assignment' => 25, 'Assessment' => 25, 'Exercise' => 25]);
   ```

### Missing Activity Type Settings

Run sync command:
```php
GradeSetting::syncActivityTypeSettings();
```

## Example Scenarios

### Scenario 1: Standard Course
- 3 modules, each with lessons and all 4 activity types
- Module weights: 33.33% each
- Lesson weight: 20%, Activity weight: 80%
- Activity type weights: Quiz 30%, Assignment 15%, Assessment 35%, Exercise 20%

### Scenario 2: Quiz-Only Course
- 5 modules, each with only quizzes
- Module weights: 20% each
- Lesson weight: 0% (no lessons)
- Activity weight: 100% (only activities)
- Quiz weight: 100% (normalized, only type present)

### Scenario 3: Hybrid Module
- Module with lessons, quizzes, and assignments (no assessments/exercises)
- Lesson weight: 20%
- Activity weight: 80%
- Normalized activity weights: Quiz 66.67% (30/45), Assignment 33.33% (15/45)

## API Endpoints

### Get Student Grade Report
```
GET /student/grades/report?course_id={courseId}
```

### Get Grade Settings
```
GET /api/grade-settings/global
GET /api/grade-settings/course/{courseId}
```

## Future Enhancements

- Grade trending and analytics
- Custom grading scales per course
- Weighted module importance
- Extra credit support
- Grade curves and adjustments
- Instructor comments and feedback integration

## Related Documentation

- [COURSE_GRADE_SETTINGS_IMPLEMENTATION.md](./COURSE_GRADE_SETTINGS_IMPLEMENTATION.md)
- [Activity Management Guide](./ACTIVITY_MANAGEMENT_GUIDE.md)
- [Course Services Guide](./COURSE_SERVICES_GUIDE.md)

---

**Last Updated**: March 4, 2026
**Version**: 2.0 (Dynamic Activity Type Integration)
