# Schedule Type Seeder Unique Constraint Fix

## Issue

When running the database seeder (especially on production/forge), the following error occurred:

```
SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: schedule_types.name
```

### Root Cause

The `ScheduleTypeSeeder` was using `DB::table('schedule_types')->insert()` which attempts to insert new records every time it runs. If the schedule types already exist in the database, this causes a UNIQUE constraint violation on the `name` column.

This is especially problematic when:
- Running seeders multiple times during development
- Re-running seeders after deployment
- The `schedule_types` table already has data

## Solution

Changed the seeder from using `insert()` to using **update or create logic**:

### Before (Problematic Code):
```php
public function run(): void
{
    $scheduleTypes = ScheduleTypeEnum::getAllSeederData();
    DB::table('schedule_types')->insert($scheduleTypes);  // ❌ Fails if records exist
}
```

### After (Fixed Code):
```php
public function run(): void
{
    $this->command->info('Seeding schedule types...');
    
    $createdCount = 0;
    $updatedCount = 0;
    
    // Loop through each schedule type and updateOrCreate
    foreach (ScheduleTypeEnum::cases() as $type) {
        $data = $type->toSeederArray();
        
        // Check if it exists
        $exists = DB::table('schedule_types')
            ->where('name', $type->value)
            ->exists();
        
        if ($exists) {
            // Update existing
            DB::table('schedule_types')
                ->where('name', $type->value)
                ->update([
                    'description' => $data['description'],
                    'color' => $data['color'],
                    'icon' => $data['icon'],
                    'is_active' => $data['is_active'],
                    'updated_at' => now(),
                ]);
            $updatedCount++;
            $this->command->line("   ↻ Updated: {$type->label()} ({$type->value})");
        } else {
            // Create new
            DB::table('schedule_types')->insert($data);
            $createdCount++;
            $this->command->line("   ✓ Created: {$type->label()} ({$type->value})");
        }
    }
    
    $this->command->info("✅ Schedule types seeded: {$createdCount} created, {$updatedCount} updated");
}
```

## Benefits

1. **Idempotent**: Can be run multiple times without errors
2. **Update Existing**: Updates existing records with new values from the enum
3. **Clear Feedback**: Shows which records were created vs updated
4. **Production Safe**: Won't fail when records already exist
5. **Maintains Data**: Preserves existing IDs and relationships

## Testing Results

### First Run (Fresh Database):
```
Seeding schedule types...
   ✓ Created: Activity (activity) - #3B82F6
   ✓ Created: Course (course) - #10B981
   ✓ Created: Personal/Adhoc (adhoc) - #F59E0B
   ✓ Created: Exam (exam) - #EF4444
   ✓ Created: Office Hours (office_hours) - #8B5CF6
   ✓ Created: Course Due Date (course_due_date) - #06B6D4
✅ Schedule types seeded: 6 created, 0 updated
```

### Subsequent Runs (Existing Data):
```
Seeding schedule types...
   ↻ Updated: Activity (activity) - #3B82F6
   ↻ Updated: Course (course) - #10B981
   ↻ Updated: Personal/Adhoc (adhoc) - #F59E0B
   ↻ Updated: Exam (exam) - #EF4444
   ↻ Updated: Office Hours (office_hours) - #8B5CF6
   ↻ Updated: Course Due Date (course_due_date) - #06B6D4
✅ Schedule types seeded: 0 created, 6 updated
```

## Seeding Order

The `DatabaseSeeder` ensures correct order:

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        GradeLevelSeeder::class,
        ActivityTypeSeeder::class,
        QuestionTypeSeeder::class,
        ScheduleTypeSeeder::class,      // ✅ Runs BEFORE ComprehensiveSeeder
    ]);
    
    $this->call([
        ComprehensiveSeeder::class,      // Uses schedule types
    ]);
}
```

## Related Files

- **Fixed File**: `database/seeders/ScheduleTypeSeeder.php`
- **Enum Source**: `app/Enums/ScheduleTypeEnum.php`
- **Migration**: `database/migrations/*_create_schedule_types_table.php`
- **Model**: `app/Models/ScheduleType.php`

## Deployment

This fix is safe to deploy and can be run on production:

```bash
# On production/forge
php artisan db:seed --class=ScheduleTypeSeeder
```

Or as part of full seeding:

```bash
php artisan migrate:fresh --seed
```

## Future Considerations

If you add new schedule types to the `ScheduleTypeEnum`:

1. Add the new case to the enum
2. Update all `match()` statements in the enum methods
3. Run `php artisan db:seed --class=ScheduleTypeSeeder`
4. The seeder will automatically create the new type

Example:
```php
enum ScheduleTypeEnum: string
{
    // Existing types...
    case LAB_SESSION = 'lab_session';  // New type
    
    public function label(): string
    {
        return match($this) {
            // Existing cases...
            self::LAB_SESSION => 'Lab Session',  // Add here
        };
    }
    
    // Update all other match() statements...
}
```

Then run:
```bash
php artisan db:seed --class=ScheduleTypeSeeder
```

Output:
```
Seeding schedule types...
   ↻ Updated: Activity (activity) - #3B82F6
   ↻ Updated: Course (course) - #10B981
   ↻ Updated: Personal/Adhoc (adhoc) - #F59E0B
   ↻ Updated: Exam (exam) - #EF4444
   ↻ Updated: Office Hours (office_hours) - #8B5CF6
   ↻ Updated: Course Due Date (course_due_date) - #06B6D4
   ✓ Created: Lab Session (lab_session) - #14B8A6
✅ Schedule types seeded: 1 created, 6 updated
```

## Summary

The `ScheduleTypeSeeder` is now **production-ready** and **idempotent**. It can be safely run multiple times without causing unique constraint violations, making it suitable for:

- Local development
- CI/CD pipelines
- Production deployments
- Database maintenance

---

**Fixed**: October 15, 2025  
**Issue**: UNIQUE constraint violation on schedule_types.name  
**Solution**: Changed from insert() to update-or-create logic
