# Activity Management & Skill Syncing - Complete Implementation

## Changes Summary

### 1. Automatic Skill-Activity Synchronization (NEW)

Two new Model Observers automatically maintain skill-activity relationships:

#### **ActivityObserver** (`app/Observers/ActivityObserver.php`)
- **Trigger**: When an Activity is created or updated
- **Function**: Automatically links the activity to all skills in its assigned modules
- **Behavior**: 
  - On `create`: Links activity to all module skills with weight=1.0
  - On `update`: Syncs skills if module relationships change

#### **ModuleObserver** (`app/Observers/ModuleObserver.php`)
- **Trigger**: When a Module is created or updated
- **Function**: Automatically links all module skills to activities in that module
- **Behavior**:
  - On `create`: Links all existing activities to all module skills
  - On `update`: Syncs skill-activity relationships

### 2. AppServiceProvider Registration
Both observers are registered in `app/Providers/AppServiceProvider.php` boot method:

```php
Activity::observe(ActivityObserver::class);
Module::observe(ModuleObserver::class);
```

## How Skill-Activity Linking Works

### Automatic Linking Flow

```
Creating a Module
  ↓
ModuleObserver::created()
  ↓
Get all activities in the module
  ↓
Get all skills in the module
  ↓
Create pivot records in skill_activities table
  ↓
Activities are now linked to module skills (weight=1.0)
```

### Creating an Activity
```
Creating an Activity with modules
  ↓
ActivityObserver::created()
  ↓
Get all modules assigned to activity
  ↓
Get all skills from those modules
  ↓
Create pivot records in skill_activities table
  ↓
Activity is now linked to all skill-module skills (weight=1.0)
```

## Enhanced Activity Display

### Unified Activity Show Page

The activity show page now displays:

#### For Quizzes:
- Quiz questions and options (QuizManagement component)
- **NEW**: Student submissions table showing:
  - Student name, email
  - Submission status (not_started, in_progress, submitted, graded)
  - Progress bar and percentage
  - Score out of max
  - Submission date

#### For Assignments:
- Assignment questions (AssignmentManagement component)
- Student submissions table (unchanged)

#### For Other Activity Types:
- **NEW**: Generic student submissions table showing:
  - Status, progress, scores
  - Works with any activity type

### Student Submissions Display (All Activity Types)

**Location**: `/activities/{id}` (Activity show page)

**Data Shown**:
```
Student Name | Email | Status | Progress | Score | Submission Date
─────────────────────────────────────────────────────────────────
Student 1   | ...   | graded | 100%     | 85/100| 2026-03-04
Student 2   | ...   | in_progress | 50% | —     | —
Student 3   | ...   | not_started | 0% | —     | —
```

**Status Color Coding**:
- 🟡 Yellow: in_progress
- 🔵 Blue: submitted
- 🟢 Green: graded
- ⚪ Gray: not_started

## Database Structure

### skill_activities Pivot Table
```
activity_id (FK) | skill_id (FK) | weight
──────────────────────────────────────────
1                | 2             | 1.0
1                | 3             | 1.0
2                | 1             | 1.0
2                | 3             | 1.0
```

## API Changes

### ActivityController::show()
- **Before**: Only fetched assignment progress
- **After**: Fetches student progress for ANY activity type

**Data Returned**:
```json
{
  "activity": { ... },
  "studentsProgress": [
    {
      "student_id": 1,
      "student_name": "Student 1",
      "student_email": "student1@example.com",
      "status": "graded",
      "score": 85,
      "max_score": 100,
      "percentage_score": 85.0,
      "progress_percentage": 100.0,
      "submitted_at": "2026-03-04 12:00:00",
      "graded_at": "2026-03-04 13:00:00"
    }
  ]
}
```

## Testing the Implementation

### Verify Auto-Linking Works

1. **Create a module with skills**:
   ```
   Course → Module → Skills (Content Mastery, Application, Advanced Thinking)
   ```

2. **Create an activity in that module**:
   ```
   Activity → Assign to Module
   ```

3. **Check skill_activities table**:
   ```bash
   sqlite> SELECT COUNT(*) FROM skill_activities;
   # Should show new links
   ```

4. **Or check in database**:
   ```sql
   SELECT a.title, COUNT(sa.skill_id) as skill_count
   FROM activities a
   LEFT JOIN skill_activities sa ON a.id = sa.activity_id
   GROUP BY a.id;
   ```

### View Activity Results as Instructor

1. Navigate to `/activity-management`
2. Click "View" on any activity (e.g., Activity 2)
3. Scroll down to "Activity Details" section
4. See collapsible "Assignment Details" or "Quiz Questions"  
5. Scroll further to see **"Student Submissions"** table
6. Table shows:
   - All students enrolled in the course
   - Their submission status, progress, and scores
   - Dates of submission and grading

## Key Features

### 1. Automatic Skill Syncing
- ✅ No manual linking needed for module/activity creation
- ✅ Handles bi-directional relationships
- ✅ Uses default weight of 1.0 (can be customized later)
- ✅ Preserves existing relationships (uses `syncWithoutDetaching`)

### 2. Unified Display Pattern
- ✅ All activity types show student submissions
- ✅ Consistent UI across Quiz, Assignment, and custom types
- ✅ Shows progress bars, status badges, scores
- ✅ Sortable and filterable table

### 3. Data Consistency
- ✅ Skills are always linked when activities/modules are created
- ✅ No orphaned activities without skill associations
- ✅ Assessment calculations work correctly

## Configuration

### Custom Skill Weights

Instructors can still customize skill weights through the UI:

**Edit Activity Skills**:
```
GET /api/activities/{id}/skills
# View current skill associations

POST /api/activities/{id}/skills
# Add new skill with custom weight

PUT /api/activities/{id}/skills/{skill_id}
# Update existing skill weight
```

### Default Weight

Currently set to `1.0` in observers. To change:

**ActivityObserver.php**:
```php
$pivotData[$skillId] = ['weight' => 1.5]; // Change from 1.0 to 1.5
```

## Troubleshooting

### Skills Not Showing in Submissions

**Cause**: Observer not registered

**Fix**: Verify in `AppServiceProvider.php`:
```php
Activity::observe(ActivityObserver::class);
Module::observe(ModuleObserver::class);
```

### Missing Student Submissions

**Cause**: No student activities recorded yet

**Fix**: Have students complete the activity first, or check:
```bash
sqlite> SELECT COUNT(*) FROM student_activities WHERE activity_id = 2;
```

### Skills Not Auto-Linking

**Cause**: Activity or module created before observers registered

**Fix**: Run seeder to manually link:
```bash
php artisan db:seed --class=SkillActivityLinkerSeeder
```

## Performance Considerations

### Observer Overhead
- Minimal: Only runs on create/update events
- Each observer call is ~1-2 queries
- No significant performance impact

### Query Optimization
- Activity show page uses `load()` with eager loading
- Student progress fetched efficiently per course
- Pagination not needed (typically <50 students per course)

## Future Enhancements

1. **Smart Weight Assignment**: Base weights on activity type, difficulty
2. **Bulk Operations**: Link multiple activities to multiple skills at once
3. **Weight Suggestions**: ML-based weight recommendations
4. **Activity Recommendations**: Suggest which skills should be linked
5. **Import/Export**: Bulk skill-activity mapping via CSV

## Related Files

### Modified Files
- `app/Http/Controllers/ActivityController.php` - Enhanced show() method
- `resources/js/pages/ActivityManagement/Show.vue` - Unified submissions display
- `app/Providers/AppServiceProvider.php` - Observer registration

### New Files
- `app/Observers/ActivityObserver.php`
- `app/Observers/ModuleObserver.php`

### Existing Related Files
- `database/seeders/SkillActivityLinkerSeeder.php` - Manual linking
- `database/seeders/SkillSeeder.php` - Initial seeding
- `app/Http/Controllers/Instructor/ActivitySkillController.php` - Manual UI updates
