# PHP Tinker Commands for Grade Levels

## Get Unique Grade Levels from Students

### Command 1: Get all unique grade levels
```php
\App\Models\User::whereNotNull('grade_level')->distinct()->pluck('grade_level');
```

### Command 2: Get unique grade levels with count
```php
\App\Models\User::whereNotNull('grade_level')->groupBy('grade_level')->selectRaw('grade_level, count(*) as count')->get();
```

### Command 3: Get unique grade levels ordered
```php
\App\Models\User::whereNotNull('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');
```

### Command 4: Get grade levels as array
```php
\App\Models\User::whereNotNull('grade_level')->distinct()->pluck('grade_level')->toArray();
```

### Command 5: Get students by grade level
```php
\App\Models\User::where('grade_level', 'Grade 7')->get(['id', 'name', 'email', 'grade_level', 'section']);
```

### Command 6: Get all grade levels with students count
```php
\App\Models\User::whereNotNull('grade_level')->select('grade_level', \DB::raw('count(*) as total'))->groupBy('grade_level')->orderBy('grade_level')->get();
```

## Get Grade Levels from Courses

### Command 7: Get unique grade levels from courses
```php
\App\Models\Course::whereNotNull('grade_level')->distinct()->pluck('grade_level');
```

### Command 8: Get courses per grade level
```php
\App\Models\Course::select('grade_level', \DB::raw('count(*) as total'))->whereNotNull('grade_level')->groupBy('grade_level')->orderBy('grade_level')->get();
```

## Usage Examples

### Example 1: Simple list
Open tinker:
```bash
php artisan tinker
```

Then run:
```php
User::whereNotNull('grade_level')->distinct()->pluck('grade_level');
```

Output:
```
Illuminate\Support\Collection {#4520
  all: [
    "Grade 7",
    "Grade 8",
    "Grade 9",
    "Grade 10",
    "Grade 11",
    "Grade 12",
  ],
}
```

### Example 2: With counts
```php
User::whereNotNull('grade_level')->select('grade_level', \DB::raw('count(*) as total'))->groupBy('grade_level')->orderBy('grade_level')->get();
```

Output:
```
Illuminate\Database\Eloquent\Collection {#4521
  all: [
    App\Models\User {#4522
      grade_level: "Grade 7",
      total: 1,
    },
    App\Models\User {#4523
      grade_level: "Grade 8",
      total: 2,
    },
    // ... more grades
  ],
}
```

### Example 3: As JSON
```php
User::whereNotNull('grade_level')->distinct()->pluck('grade_level')->toJson();
```

Output:
```
"["Grade 7","Grade 8","Grade 9","Grade 10","Grade 11","Grade 12"]"
```

## Advanced Queries

### Get grade distribution with sections
```php
User::whereNotNull('grade_level')
    ->select('grade_level', 'section', \DB::raw('count(*) as total'))
    ->groupBy('grade_level', 'section')
    ->orderBy('grade_level')
    ->orderBy('section')
    ->get();
```

### Get students per grade with their courses
```php
User::with('enrollments.course')
    ->whereNotNull('grade_level')
    ->where('grade_level', 'Grade 10')
    ->get();
```

### Get complete grade level summary
```php
collect([
    'grade_levels' => User::whereNotNull('grade_level')->distinct()->pluck('grade_level'),
    'students_count' => User::whereNotNull('grade_level')->count(),
    'distribution' => User::whereNotNull('grade_level')
        ->select('grade_level', \DB::raw('count(*) as total'))
        ->groupBy('grade_level')
        ->orderBy('grade_level')
        ->pluck('total', 'grade_level'),
]);
```

## Quick Reference

| Command | Description |
|---------|-------------|
| `User::distinct()->pluck('grade_level')` | Simple list |
| `User::groupBy('grade_level')->selectRaw('grade_level, count(*) as count')->get()` | With counts |
| `User::where('grade_level', 'Grade 7')->count()` | Count specific grade |
| `Course::distinct()->pluck('grade_level')` | Course grade levels |

## Exit Tinker
```php
exit
```
or press `Ctrl + C`
