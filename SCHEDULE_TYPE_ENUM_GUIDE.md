# Schedule Type Enum - Usage Guide

## Overview
The `ScheduleTypeEnum` provides a type-safe, centralized way to manage schedule types across the application. This eliminates magic strings and ensures consistency.

## File Location
```
app/Enums/ScheduleTypeEnum.php
```

## Available Schedule Types

| Enum Case | Value | Label | Color | Icon | Description |
|-----------|-------|-------|-------|------|-------------|
| `ACTIVITY` | `activity` | Activity | #3B82F6 (Blue) | clipboard-list | Quizzes, assignments, assessments |
| `COURSE` | `course` | Course | #10B981 (Green) | book-open | Course lectures, seminars, sessions |
| `ADHOC` | `adhoc` | Personal/Adhoc | #F59E0B (Amber) | calendar | Personal events, meetings |
| `EXAM` | `exam` | Exam | #EF4444 (Red) | file-text | Formal examinations and tests |
| `OFFICE_HOURS` | `office_hours` | Office Hours | #8B5CF6 (Purple) | users | Instructor office hours |
| `COURSE_DUE_DATE` | `course_due_date` | Course Due Date | #06B6D4 (Cyan) | calendar-check | Auto-generated course schedules |

## Basic Usage

### 1. Getting Schedule Type Properties

```php
use App\Enums\ScheduleTypeEnum;

// Get label
$label = ScheduleTypeEnum::ACTIVITY->label(); // "Activity"

// Get description
$description = ScheduleTypeEnum::EXAM->description();

// Get color
$color = ScheduleTypeEnum::COURSE->color(); // "#10B981"

// Get icon
$icon = ScheduleTypeEnum::OFFICE_HOURS->icon(); // "users"

// Get Lucide Vue icon component name
$lucideIcon = ScheduleTypeEnum::ACTIVITY->lucideIcon(); // "ClipboardList"

// Get enum value
$value = ScheduleTypeEnum::ADHOC->value; // "adhoc"
```

### 2. Converting String to Enum

```php
// Safe conversion (returns null if invalid)
$type = ScheduleTypeEnum::fromString('activity'); // ScheduleTypeEnum::ACTIVITY
$invalid = ScheduleTypeEnum::fromString('invalid'); // null

// Check if valid
if (ScheduleTypeEnum::isValid('exam')) {
    // Valid schedule type
}
```

### 3. Getting All Schedule Types

```php
// Get all enum cases
$allCases = ScheduleTypeEnum::cases();

// Get all values as array
$values = ScheduleTypeEnum::values(); 
// ['activity', 'course', 'adhoc', 'exam', 'office_hours', 'course_due_date']

// Get as array with all properties
$array = ScheduleTypeEnum::toArray();
// [
//     ['value' => 'activity', 'label' => 'Activity', 'color' => '#3B82F6', ...],
//     ['value' => 'course', 'label' => 'Course', 'color' => '#10B981', ...],
//     ...
// ]
```

## Controller Usage

### Creating Schedules with Enum

```php
use App\Enums\ScheduleTypeEnum;
use App\Models\Schedule;
use App\Models\ScheduleType;

class ScheduleController extends Controller
{
    public function createActivitySchedule(Request $request)
    {
        // Get schedule type using enum
        $scheduleTypeEnum = ScheduleTypeEnum::ACTIVITY;
        
        // Get or create schedule type in database
        $scheduleType = ScheduleType::firstOrCreate(
            ['name' => $scheduleTypeEnum->value],
            [
                'name' => $scheduleTypeEnum->value,
                'description' => $scheduleTypeEnum->description(),
                'color' => $scheduleTypeEnum->color(),
                'icon' => $scheduleTypeEnum->icon(),
                'is_active' => true,
            ]
        );

        // Create schedule
        $schedule = Schedule::create([
            'schedule_type_id' => $scheduleType->id,
            'title' => $request->title,
            'from_datetime' => $request->start_date,
            'to_datetime' => $request->end_date,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        return response()->json($schedule);
    }
}
```

### Example: Course Due Date Schedule (from CourseController)

```php
private function createOrUpdateCourseSchedule(Course $course, array $data)
{
    try {
        // Use enum for type-safe schedule type
        $scheduleTypeEnum = ScheduleTypeEnum::COURSE_DUE_DATE;
        
        $scheduleType = ScheduleType::firstOrCreate(
            ['name' => $scheduleTypeEnum->value],
            [
                'name' => $scheduleTypeEnum->value,
                'description' => $scheduleTypeEnum->description(),
                'color' => $scheduleTypeEnum->color(),
                'icon' => $scheduleTypeEnum->icon(),
                'is_active' => true,
            ]
        );

        $endDate = new \DateTime($data['end_date']);
        $fromDate = clone $endDate;
        $fromDate->modify('-1 hour');

        $scheduleData = [
            'schedule_type_id' => $scheduleType->id,
            'title' => $course->title . ' - Due Date',
            'description' => 'Course due date for ' . $course->title,
            'from_datetime' => $fromDate->format('Y-m-d H:i:s'),
            'to_datetime' => $endDate->format('Y-m-d H:i:s'),
            'status' => 'scheduled',
            'created_by' => auth()->id(),
            'schedulable_type' => Course::class,
            'schedulable_id' => $course->id,
        ];

        $schedule = Schedule::updateOrCreate(
            [
                'schedulable_type' => Course::class,
                'schedulable_id' => $course->id,
            ],
            $scheduleData
        );

        return $schedule;
    } catch (\Exception $e) {
        Log::error('Failed to create/update course schedule', [
            'course_id' => $course->id,
            'error' => $e->getMessage()
        ]);
        return null;
    }
}
```

## Model Usage with HasScheduleType Trait

### Using the Trait

```php
use App\Models\Schedule;
use App\Enums\ScheduleTypeEnum;

$schedule = Schedule::find(1);

// Get enum instance
$typeEnum = $schedule->getScheduleTypeEnum(); // ScheduleTypeEnum instance

// Check schedule type
if ($schedule->isActivity()) {
    // Handle activity schedule
}

if ($schedule->isCourse()) {
    // Handle course schedule
}

if ($schedule->isExam()) {
    // Handle exam schedule
}

// Get type properties through trait
$color = $schedule->getTypeColor(); // "#3B82F6"
$icon = $schedule->getTypeIcon();   // "clipboard-list"
$label = $schedule->getTypeLabel(); // "Activity"

// Check specific type
if ($schedule->isType(ScheduleTypeEnum::OFFICE_HOURS)) {
    // This is office hours
}
```

### Query Scopes

```php
use App\Models\Schedule;
use App\Enums\ScheduleTypeEnum;

// Get all activity schedules
$activities = Schedule::ofType(ScheduleTypeEnum::ACTIVITY)->get();

// Get all exams
$exams = Schedule::ofType(ScheduleTypeEnum::EXAM)->get();

// Get multiple types
$coursesAndExams = Schedule::ofTypes([
    ScheduleTypeEnum::COURSE,
    ScheduleTypeEnum::EXAM
])->get();

// Combine with other queries
$upcomingExams = Schedule::ofType(ScheduleTypeEnum::EXAM)
    ->where('from_datetime', '>', now())
    ->orderBy('from_datetime')
    ->get();
```

## Validation Usage

### Form Request Validation

```php
use App\Enums\ScheduleTypeEnum;
use Illuminate\Validation\Rule;

class CreateScheduleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'schedule_type' => [
                'required',
                Rule::in(ScheduleTypeEnum::values())
            ],
            'from_datetime' => 'required|date',
            'to_datetime' => 'required|date|after:from_datetime',
        ];
    }

    public function messages()
    {
        return [
            'schedule_type.in' => 'Invalid schedule type. Valid types are: ' 
                . implode(', ', ScheduleTypeEnum::values()),
        ];
    }
}
```

## Seeder Usage

### Updated ScheduleTypeSeeder

```php
use App\Enums\ScheduleTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all schedule types from enum - single source of truth!
        $scheduleTypes = ScheduleTypeEnum::getAllSeederData();

        DB::table('schedule_types')->insert($scheduleTypes);
        
        $count = count($scheduleTypes);
        $this->command->info("✅ {$count} schedule types seeded successfully!");
    }
}
```

## Frontend Integration

### API Response Format

When returning schedules to frontend:

```php
public function index(Request $request)
{
    $schedules = Schedule::with('scheduleType')
        ->where('created_by', auth()->id())
        ->get()
        ->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'from_datetime' => $schedule->from_datetime,
                'to_datetime' => $schedule->to_datetime,
                'type' => [
                    'value' => $schedule->scheduleType->name,
                    'label' => $schedule->getTypeLabel(),
                    'color' => $schedule->getTypeColor(),
                    'icon' => $schedule->getTypeIcon(),
                    'lucide_icon' => $schedule->getScheduleTypeEnum()?->lucideIcon(),
                ],
            ];
        });

    return response()->json($schedules);
}
```

### Vue.js Component Usage

```vue
<template>
  <div class="schedule-item" :style="{ borderLeft: `4px solid ${schedule.type.color}` }">
    <component :is="getIconComponent(schedule.type.lucide_icon)" class="w-5 h-5" />
    <span>{{ schedule.title }}</span>
    <span class="badge" :style="{ backgroundColor: schedule.type.color }">
      {{ schedule.type.label }}
    </span>
  </div>
</template>

<script setup>
import { ClipboardList, BookOpen, Calendar, FileText, Users, CalendarCheck } from 'lucide-vue-next';

const iconComponents = {
  ClipboardList,
  BookOpen,
  Calendar,
  FileText,
  Users,
  CalendarCheck,
};

const getIconComponent = (iconName) => iconComponents[iconName] || Calendar;
</script>
```

## Adding New Schedule Types

To add a new schedule type:

1. **Update the Enum** (`app/Enums/ScheduleTypeEnum.php`):

```php
enum ScheduleTypeEnum: string
{
    // ... existing types
    case LAB_SESSION = 'lab_session';
    
    // ... update match statements in all methods
    public function label(): string
    {
        return match($this) {
            // ... existing cases
            self::LAB_SESSION => 'Lab Session',
        };
    }
    
    public function description(): string
    {
        return match($this) {
            // ... existing cases
            self::LAB_SESSION => 'Hands-on laboratory sessions',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            // ... existing cases
            self::LAB_SESSION => '#14B8A6', // Teal
        };
    }
    
    public function icon(): string
    {
        return match($this) {
            // ... existing cases
            self::LAB_SESSION => 'flask-conical',
        };
    }
}
```

2. **Add Helper Method to Trait** (optional):

```php
// In app/Traits/HasScheduleType.php
public function isLabSession(): bool
{
    return $this->isType(ScheduleTypeEnum::LAB_SESSION);
}
```

3. **Re-run the Seeder**:

```bash
php artisan db:seed --class=ScheduleTypeSeeder
```

That's it! The new type is now available throughout the application.

## Benefits of Using Enum

### ✅ Type Safety
- IDE autocomplete support
- Compile-time checking (no typos)
- Refactoring safety

### ✅ Single Source of Truth
- All properties defined in one place
- Consistent colors, icons, and labels
- Easy to maintain and update

### ✅ Self-Documenting
- Clear relationship between types and properties
- No magic strings scattered in code
- Easy to understand for new developers

### ✅ Database Consistency
- Seeder automatically uses enum values
- Validation rules automatically generated
- No manual synchronization needed

## Migration Considerations

If you have existing schedules with hardcoded schedule types:

```php
use App\Enums\ScheduleTypeEnum;
use App\Models\ScheduleType;

// Map old values to new enum values
$mappings = [
    'Course Due Date' => ScheduleTypeEnum::COURSE_DUE_DATE->value,
    'Activity Schedule' => ScheduleTypeEnum::ACTIVITY->value,
    // ... etc
];

// Update schedule_types table
foreach ($mappings as $oldName => $newName) {
    ScheduleType::where('name', $oldName)->update(['name' => $newName]);
}
```

## Testing

```php
use App\Enums\ScheduleTypeEnum;
use Tests\TestCase;

class ScheduleTypeEnumTest extends TestCase
{
    public function test_all_schedule_types_have_required_properties()
    {
        foreach (ScheduleTypeEnum::cases() as $type) {
            $this->assertNotEmpty($type->label());
            $this->assertNotEmpty($type->description());
            $this->assertNotEmpty($type->color());
            $this->assertNotEmpty($type->icon());
            $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $type->color());
        }
    }

    public function test_from_string_returns_correct_enum()
    {
        $type = ScheduleTypeEnum::fromString('activity');
        $this->assertEquals(ScheduleTypeEnum::ACTIVITY, $type);
    }

    public function test_is_valid_checks_schedule_type()
    {
        $this->assertTrue(ScheduleTypeEnum::isValid('exam'));
        $this->assertFalse(ScheduleTypeEnum::isValid('invalid_type'));
    }
}
```

## Summary

The `ScheduleTypeEnum` provides a robust, maintainable way to manage schedule types. Use it whenever you need to:

- Create schedules
- Filter by schedule type
- Display schedule type information
- Validate schedule types
- Seed schedule types
- Check schedule type properties

All schedule type properties are centralized in one place, making the codebase more maintainable and less error-prone.
