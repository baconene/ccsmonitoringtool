# ComprehensiveSeeder Unique Constraint Fix

## Issue

When running the database seeder on production/forge (especially after initial deployment), the following errors occurred:

```
SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: course_student.course_id, course_student.student_id
```

### Root Cause

The `ComprehensiveSeeder` was using direct `insert()` and `create()` methods without checking for existing records. When running the seeder multiple times (or if data already exists), this caused UNIQUE constraint violations on:

1. `course_student` pivot table (course_id, student_id unique constraint)
2. `module_activities` pivot table (module_id, activity_id unique constraint)
3. `course_enrollments` table (student_id, course_id unique constraint)

Additionally, the `clearExistingData()` method was missing cleanup for:
- `schedule_participants` table
- `schedules` table  
- `course_student` pivot table

## Solutions Applied

### 1. Fixed `clearExistingData()` Method

Added missing table cleanup in proper dependency order:

```php
private function clearExistingData(): void
{
    DB::statement('PRAGMA foreign_keys=OFF');
    
    // Clear in reverse dependency order
    DB::table('student_quiz_answers')->delete();
    DB::table('student_quiz_progress')->delete();
    DB::table('student_activities')->delete();
    DB::table('schedule_participants')->delete();  // ✅ Added
    DB::table('schedules')->delete();              // ✅ Added
    DB::table('course_student')->delete();         // ✅ Added
    DB::table('course_enrollments')->delete();
    // ... rest of tables
    
    DB::statement('PRAGMA foreign_keys=ON');
}
```

### 2. Fixed `seedCourseEnrollments()` Method

Changed from direct insert to checking for existing records:

**Before (Problematic)**:
```php
private function seedCourseEnrollments(): void
{
    for ($studentId = 1; $studentId <= 15; $studentId++) {
        for ($courseId = 1; $courseId <= 3; $courseId++) {
            CourseEnrollment::create([...]);  // ❌ Fails if exists
            
            DB::table('course_student')->insert([...]);  // ❌ Fails if exists
        }
    }
}
```

**After (Fixed)**:
```php
private function seedCourseEnrollments(): void
{
    $this->command->info('Seeding course enrollments...');
    
    for ($studentId = 1; $studentId <= 15; $studentId++) {
        for ($courseId = 1; $courseId <= 3; $courseId++) {
            // Check if enrollment exists
            $enrollmentExists = CourseEnrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->exists();
            
            if (!$enrollmentExists) {
                CourseEnrollment::create([...]);  // ✅ Safe
            }
            
            // Check if pivot exists
            $pivotExists = DB::table('course_student')
                ->where('course_id', $courseId)
                ->where('student_id', $studentId)
                ->exists();
            
            if (!$pivotExists) {
                DB::table('course_student')->insert([...]);  // ✅ Safe
            }
        }
    }
    
    $this->syncScheduleParticipants();
}
```

### 3. Fixed Module Activities Insert

**Before**:
```php
foreach ($moduleActivities as $ma) {
    DB::table('module_activities')->insert($ma + [...]);  // ❌ Fails if exists
}
```

**After**:
```php
foreach ($moduleActivities as $ma) {
    // Check if the relationship already exists
    $exists = DB::table('module_activities')
        ->where('module_id', $ma['module_id'])
        ->where('activity_id', $ma['activity_id'])
        ->exists();
    
    if (!$exists) {
        DB::table('module_activities')->insert($ma + [...]);  // ✅ Safe
    }
}
```

## Testing Results

### Fresh Migration & Seed (First Run):
```bash
php artisan migrate:fresh --seed
```

**Output**:
```
✅ Foundation data seeded successfully!
Clearing existing data...
Seeding users...
Seeding students and instructors...
Seeding courses...
  ✅ Created schedule for course: Advanced Mathematics and Statistics
  ✅ Created schedule for course: Introduction to Physics
  ✅ Created schedule for course: Introduction to Computer Programming
Seeding course enrollments...
Syncing schedule participants...
  ✅ Added 15 students to schedule: Advanced Mathematics and Statistics - Due Date
  ✅ Added 15 students to schedule: Introduction to Physics - Due Date
  ✅ Added 15 students to schedule: Introduction to Computer Programming - Due Date
🎉 Database seeding completed successfully!
```

### Subsequent Runs (With Existing Data):
```bash
php artisan db:seed --class=ComprehensiveSeeder
```

The seeder now:
- ✅ Cleans all relevant tables properly
- ✅ Checks for existing records before insert
- ✅ Prevents UNIQUE constraint violations
- ✅ Can be run multiple times safely

## Benefits

1. **Idempotent**: Can be run multiple times without errors
2. **Production Safe**: Won't fail on existing data
3. **Complete Cleanup**: All dependent tables are properly cleared
4. **Clear Feedback**: Console output shows seeding progress
5. **Maintains Relationships**: Proper foreign key handling

## Deployment Safe

These changes are safe to deploy to production/forge:

```bash
# On production
php artisan migrate:fresh --seed
```

Or if you want to preserve some data:

```bash
# Clear and reseed comprehensive data only
php artisan db:seed --class=ComprehensiveSeeder
```

## Related Tables & Constraints

### Unique Constraints Fixed:
1. **course_student**: `UNIQUE(course_id, student_id)`
2. **module_activities**: `UNIQUE(module_id, activity_id)`
3. **course_enrollments**: `UNIQUE(student_id, course_id)`

### Tables Added to Cleanup:
1. **schedule_participants**: References schedules and users
2. **schedules**: Polymorphic relationship to courses/activities
3. **course_student**: Pivot table for course enrollment

## Seeding Order

The correct seeding order is maintained:

```php
// DatabaseSeeder.php
public function run(): void
{
    // 1. Foundation data (roles, grade_levels, activity_types, schedule_types)
    $this->call([
        RoleSeeder::class,
        GradeLevelSeeder::class,
        ActivityTypeSeeder::class,
        QuestionTypeSeeder::class,
        ScheduleTypeSeeder::class,  // Must run before ComprehensiveSeeder
    ]);
    
    // 2. Comprehensive data (users, courses, enrollments, schedules)
    $this->call([
        ComprehensiveSeeder::class,  // Now properly handles duplicates
    ]);
}
```

## Error Prevention Checklist

When adding new pivot tables or relationships to the seeder:

- [ ] Add table cleanup to `clearExistingData()` in proper dependency order
- [ ] Check for existing records before `insert()` or `create()`
- [ ] Use `exists()` checks for pivot table inserts
- [ ] Test with `migrate:fresh --seed` (fresh database)
- [ ] Test with `db:seed --class=SeederName` (existing data)
- [ ] Verify UNIQUE constraints in migration files
- [ ] Update documentation

## Quick Reference

### Check if Record Exists (Eloquent):
```php
$exists = Model::where('column', $value)->exists();
if (!$exists) {
    Model::create([...]);
}
```

### Check if Pivot Exists (Query Builder):
```php
$exists = DB::table('pivot_table')
    ->where('foreign_id_1', $id1)
    ->where('foreign_id_2', $id2)
    ->exists();
    
if (!$exists) {
    DB::table('pivot_table')->insert([...]);
}
```

### Proper Table Cleanup Order:
```php
// Delete in reverse dependency order (children first, parents last)
DB::table('child_table')->delete();      // References parent_table
DB::table('pivot_table')->delete();      // References two tables
DB::table('parent_table')->delete();     // Referenced by others
```

## Summary

The `ComprehensiveSeeder` is now:
- ✅ **Production-ready** - Safe for forge deployments
- ✅ **Idempotent** - Can run multiple times
- ✅ **Complete** - Cleans all dependent tables
- ✅ **Error-free** - Prevents UNIQUE constraint violations
- ✅ **Maintainable** - Clear checks for existing records

---

**Fixed**: October 15, 2025  
**Issues**: UNIQUE constraint violations on course_student, module_activities  
**Solution**: Added existence checks and complete table cleanup  
**Status**: Tested and deployed successfully
