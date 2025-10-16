# Dynamic Grade Settings System

## Overview

The Learning Management System now features a **Dynamic Grade Settings System** that allows administrators and instructors to configure how student grades are calculated without modifying code. This replaces the previous hardcoded grading weights.

## Grade Calculation Structure

### Two-Tier Weight System

The grading system uses a two-tier weighting approach:

1. **Module Component Weights** - How lessons vs activities contribute to the overall module grade
2. **Activity Type Weights** - Within activities, how each activity type (Quiz, Assignment, etc.) is weighted

### Default Configuration

**Module Components:**
- Lessons: 20%
- Activities: 80%

**Activity Types:**
- Quiz: 30%
- Assignment: 15%
- Assessment: 35%
- Exercise: 20%

## Features

### ✅ Database-Driven Configuration
- All grade weights are stored in the `grade_settings` table
- Changes take effect immediately without code deployment
- Automatic caching for optimal performance (1-hour TTL)

### ✅ Real-Time Validation
- Ensures module component weights total exactly 100%
- Ensures activity type weights total exactly 100%
- Visual indicators show validation status
- Save buttons disabled when validation fails

### ✅ Audit Trail
- Tracks who created and modified settings
- Timestamp tracking for all changes
- Maintains history of configuration changes

### ✅ Fallback Safety
- If database is unavailable, system falls back to default constants
- Prevents grading system failure
- Logs warnings when fallback is used

### ✅ Performance Optimization
- Settings cached for 1 hour to reduce database queries
- Cache automatically cleared when settings are updated
- Minimal impact on grade calculation performance

## Usage Guide

### Accessing Grade Settings

**For Administrators and Instructors:**

1. Log in to the Learning Management System
2. Click **"Grade Settings"** in the sidebar navigation
3. The Grade Settings page displays current weight configurations

### Adjusting Module Component Weights

Module components determine how lessons and activities contribute to the overall module grade.

**To Adjust:**

1. Locate the **"Module Component Weights"** card
2. Use the number inputs to set the desired percentages:
   - **Lessons**: Weight given to lesson completion (0-100%)
   - **Activities**: Weight given to activity scores (0-100%)
3. The system auto-adjusts the complementary value to maintain 100% total
4. Verify the total equals 100% (green badge indicator)
5. Click **"Save Module Weights"**

**Example:**
- If you want activities to count more: Set Activities to 90%, Lessons to 10%
- If you want equal weight: Set both to 50%

### Adjusting Activity Type Weights

Activity type weights determine how different activity types contribute when calculating the activities portion of a module grade.

**To Adjust:**

1. Locate the **"Activity Type Weights"** card
2. Use the number inputs to set percentages for each activity type:
   - **Quiz**: Weight for quiz activities
   - **Assignment**: Weight for assignment activities
   - **Assessment**: Weight for assessment activities
   - **Exercise**: Weight for exercise activities
3. Ensure the total equals 100% (shown in the badge indicator)
4. Click **"Save Activity Weights"**

**Example:**
- Emphasize quizzes: Quiz 50%, Assignment 20%, Assessment 20%, Exercise 10%
- Equal weight: All at 25%

### Resetting to Defaults

If you want to restore the factory default settings:

1. Scroll to the **"Reset to Defaults"** card
2. Click **"Reset to Defaults"** button
3. Confirm the action in the dialog
4. All weights will be restored to their original values

## How Grades are Calculated

### Module Grade Calculation

```
Module Score = (Lesson Score × Lesson Weight) + (Activity Score × Activity Weight)
```

**Example with defaults (Lessons 20%, Activities 80%):**
- Student has:
  - Lesson Score: 85%
  - Activity Score: 90%
- Calculation:
  - Lesson Contribution: 85 × 0.20 = 17 points
  - Activity Contribution: 90 × 0.80 = 72 points
  - **Module Score: 89%**

### Activity Score Calculation

Activities are weighted by type before contributing to the module grade:

```
Activity Score = Σ (Activity Type Average × Activity Type Weight)
```

**Example with defaults:**
- Student has completed:
  - Quizzes: Average 80%
  - Assignments: Average 90%
  - Assessments: Average 85%
  - Exercises: Average 75%
- Calculation:
  - Quiz Contribution: 80 × 0.30 = 24 points
  - Assignment Contribution: 90 × 0.15 = 13.5 points
  - Assessment Contribution: 85 × 0.35 = 29.75 points
  - Exercise Contribution: 75 × 0.20 = 15 points
  - **Activity Score: 82.25%**

### Complete Example

**Scenario:**
- Module Component Weights: Lessons 20%, Activities 80%
- Activity Type Weights: Quiz 30%, Assignment 15%, Assessment 35%, Exercise 20%

**Student Performance:**
- Lessons: 90% complete
- Quizzes: 85% average
- Assignments: 92% average
- Assessments: 88% average
- Exercises: 80% average

**Step 1: Calculate Activity Score**
```
Activity Score = (85 × 0.30) + (92 × 0.15) + (88 × 0.35) + (80 × 0.20)
               = 25.5 + 13.8 + 30.8 + 16
               = 86.1%
```

**Step 2: Calculate Module Grade**
```
Module Score = (90 × 0.20) + (86.1 × 0.80)
             = 18 + 68.88
             = 86.88%
```

**Final Grade: 86.88% (B)**

## Technical Architecture

### Database Structure

**Table: `grade_settings`**

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `setting_type` | enum | Type: 'module_component' or 'activity_type' |
| `setting_key` | string | Key: 'lessons', 'Quiz', etc. |
| `display_name` | string | Human-readable name |
| `weight_percentage` | decimal(5,2) | Weight value (0-100) |
| `is_active` | boolean | Enable/disable setting |
| `created_by` | foreign key | User who created |
| `updated_by` | foreign key | User who last updated |
| `created_at` | timestamp | Creation time |
| `updated_at` | timestamp | Last update time |

**Unique Constraint:** (`setting_type`, `setting_key`)

### Backend Components

**1. Model: `App\Models\GradeSetting`**
- Eloquent model with caching
- Validation methods
- Static getter methods for weights
- Automatic cache management

**2. Controller: `App\Http\Controllers\GradeSettingsController`**
- `index()` - Display settings page
- `updateModuleComponents()` - Update lesson/activity weights
- `updateActivityTypes()` - Update activity type weights
- `reset()` - Reset to defaults

**3. Service: `App\Services\GradeCalculatorService`**
- Reads weights from database (with caching)
- Falls back to default constants if needed
- Calculates student grades using dynamic weights
- Logs errors for monitoring

### Frontend Components

**Vue Component: `resources/js/Pages/Admin/GradeSettings.vue`**
- Real-time weight adjustment
- Live validation feedback
- Auto-calculation of totals
- Interactive number inputs
- Toast notifications for success/errors

### Routes

| Method | URI | Controller Method | Access |
|--------|-----|-------------------|--------|
| GET | `/grade-settings` | `index` | Instructor, Admin |
| POST | `/grade-settings/module-components` | `updateModuleComponents` | Instructor, Admin |
| POST | `/grade-settings/activity-types` | `updateActivityTypes` | Instructor, Admin |
| POST | `/grade-settings/reset` | `reset` | Instructor, Admin |

### Caching Strategy

**Cache Keys:**
- `grade_settings.module_components` - Module component weights
- `grade_settings.activity_types` - Activity type weights

**Cache Duration:** 1 hour (3600 seconds)

**Cache Invalidation:**
- Automatic on save/update
- Automatic on delete
- Manual: `php artisan cache:clear`

## API Usage (Optional)

For programmatic access to grade settings:

```php
use App\Models\GradeSetting;

// Get module component weights
$moduleWeights = GradeSetting::getModuleComponentWeights();
// Returns: ['lessons' => 20, 'activities' => 80]

// Get activity type weights
$activityWeights = GradeSetting::getActivityTypeWeights();
// Returns: ['Quiz' => 30, 'Assignment' => 15, 'Assessment' => 35, 'Exercise' => 20]
```

## Best Practices

### Recommended Weight Configurations

**Scenario 1: Theory-Heavy Course**
- Lessons: 40%, Activities: 60%
- Emphasizes content comprehension

**Scenario 2: Practice-Heavy Course**
- Lessons: 10%, Activities: 90%
- Emphasizes hands-on work

**Scenario 3: Assessment-Focused**
- Activity Types: Quiz 20%, Assignment 10%, Assessment 60%, Exercise 10%
- Emphasizes formal assessments

**Scenario 4: Continuous Assessment**
- Activity Types: Quiz 25%, Assignment 25%, Assessment 25%, Exercise 25%
- Equal weight to all activity types

### Important Considerations

1. **Communication**: Inform students of grading weights before the course begins
2. **Consistency**: Avoid changing weights mid-course unless absolutely necessary
3. **Documentation**: Keep a record of weight changes and the reasoning behind them
4. **Testing**: After changes, verify grades calculate as expected
5. **Backup**: Note previous settings before making changes

## Troubleshooting

### Issue: "Weights don't total 100%"

**Solution:**
- Adjust values so the total equals exactly 100
- Use the auto-adjustment feature for module components
- Manually distribute percentages for activity types

### Issue: "Changes not reflecting in student grades"

**Solution:**
1. Clear the cache: `php artisan cache:clear`
2. Verify settings were saved successfully
3. Check for any error messages in the toast notifications

### Issue: "Save button is disabled"

**Solution:**
- This means validation failed
- Check that totals equal 100% (shown in badge)
- Adjust weights to meet the requirement

### Issue: "System shows default values after update"

**Solution:**
1. Check database connectivity
2. Review Laravel logs: `storage/logs/laravel.log`
3. Verify migration was run: `php artisan migrate:status`

## Maintenance

### Database Backup

Before making significant changes:

```bash
# Backup grade settings
php artisan db:table grade_settings --export
```

### Monitoring

Check logs for fallback usage:

```bash
# View recent logs
tail -f storage/logs/laravel.log | grep "grade_settings"
```

### Performance

Monitor cache hit rates:

```bash
# Check cache stats (if using Redis)
redis-cli INFO stats
```

## Migration History

**Migration:** `2025_10_17_034317_create_grade_settings_table.php`

**Included:**
- Table schema creation
- Default value seeding
- Foreign key constraints
- Unique constraints

## Future Enhancements

Potential features for future versions:

- [ ] Course-specific grade settings (different weights per course)
- [ ] Historical weight tracking with change log
- [ ] Import/Export of grade configurations
- [ ] Visual grade weight previews (pie charts)
- [ ] Student-facing grade weight display
- [ ] Role-based weight adjustment permissions
- [ ] A/B testing of different weight configurations
- [ ] Preset weight templates for common scenarios

## Support

For technical issues or questions:

1. Check this documentation
2. Review Laravel logs
3. Contact system administrator
4. Raise a support ticket with IT

---

**Last Updated:** January 2025  
**Version:** 1.0  
**Author:** LMS Development Team
