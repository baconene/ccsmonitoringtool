# Database Seeding Guide

## 🌱 Initial Deployment Data

This project includes comprehensive database seeders that provide ready-to-use data for immediate deployment and testing.

## 📧 Default User Credentials

### Admin Users
- **Emails**: `admin1@test.com` through `admin10@test.com`
- **Password**: `12345678`
- **Total**: 10 admin accounts

### Instructor Users
- **Emails**: `instructor1@test.com` through `instructor10@test.com`
- **Password**: `12345678`
- **Total**: 10 instructor accounts

### Student Users
- **Emails**: `student1@test.com` through `student10@test.com`
- **Password**: `12345678`
- **Total**: 10 student accounts
- **Features**: Randomly assigned grade levels (Year 1-5, Grade 1-12) and sections (A, B, C)

## 🎯 Seeding Strategy

### Command Usage

```bash
# Fresh database with all seed data
php artisan migrate:fresh --seed

# Seed only (without migration)
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=RoleSeeder
```

### Execution Order

1. **RoleSeeder** - Creates admin, instructor, and student roles
2. **ActivityTypeSeeder** - Creates activity types (Quiz, Assignment, Exercise)
3. **GradeLevelSeeder** - Creates 17 grade levels (Year 1-5, Grade 1-12)
4. **DatabaseSeeder** - Creates users, courses, modules, and activities

## 📚 Generated Content

### Courses (5 courses)
Each course includes:
- Course title and description
- Assigned instructor (from instructor1-10)
- Target grade level
- 3-4 modules per course
- Automatic student enrollment based on grade level

**Available Courses**:
1. Introduction to Programming (Grade 9)
2. Advanced Mathematics (Grade 10)
3. Physics 101 (Grade 10)
4. Chemistry Fundamentals (Grade 9)
5. World History (Grade 11)

### Modules
Each module includes:
- Module name and sequence
- Description
- Weight percentage (equally distributed, e.g., 33.33% for 3 modules)
- Linked quiz activity (first module only)

### Quiz Activities
Each course's first module includes:
- Quiz activity linked to the module
- 2 sample questions (1 multiple-choice, 1 true-false)
- Question options with correct answers
- Passing percentage set to 70%

## 🔧 Error Handling

All seeders include comprehensive error handling:

### Constraint Error Prevention
- Foreign key checks temporarily disabled during seeding
- `updateOrCreate` used instead of `create` for idempotency
- Try-catch blocks around all database operations
- Continues execution even if individual records fail

### Benefits
- ✅ Can run seeders multiple times without errors
- ✅ Skips duplicate entries gracefully
- ✅ Updates existing records instead of failing
- ✅ Never crashes the entire seeding process
- ✅ Provides detailed feedback on what was created/skipped

## 📊 Seeding Output

After successful seeding, you'll see:

```
════════════════════════════════════════
✅ Database seeded successfully!
════════════════════════════════════════

👤 INITIAL CREDENTIALS:
────────────────────────────────────────
📧 Admins:       admin1-10@test.com
👨‍🏫 Instructors: instructor1-10@test.com
🎓 Students:    student1-10@test.com
🔑 Password:    12345678 (for all users)

📊 DATABASE SUMMARY:
────────────────────────────────────────
Users:          30 total
Courses:        5 courses
Modules:        15 modules
Activities:     5 activities
Quizzes:        5 quizzes
Grade Levels:   17 levels
Enrollments:    15 enrollments
════════════════════════════════════════
```

## 🔐 Security Notes

### Production Deployment
⚠️ **IMPORTANT**: These default credentials are for development/testing only!

Before deploying to production:
1. Change all default passwords
2. Remove or disable test accounts
3. Create proper admin accounts with strong passwords
4. Consider using environment-based seeding

```bash
# Production-safe seeding (roles and types only)
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ActivityTypeSeeder
php artisan db:seed --class=GradeLevelSeeder
```

## 🛠️ Customization

### Adding More Courses
Edit `database/seeders/DatabaseSeeder.php`:

```php
$courseData = [
    [
        'name' => 'Your Course Name',
        'description' => 'Course description',
        'grade_level' => 'Grade 9',
    ],
    // Add more courses...
];
```

### Modifying User Counts
Change the loop counters in `DatabaseSeeder.php`:

```php
// Change from 10 to your desired number
for ($i = 1; $i <= 10; $i++) {
    // User creation code...
}
```

### Custom Quiz Questions
Modify the `createQuizActivity` method in `DatabaseSeeder.php` to add more questions or change question types.

## 📝 Seeder Files

| File | Purpose |
|------|---------|
| `DatabaseSeeder.php` | Main seeder - users, courses, modules, activities |
| `RoleSeeder.php` | User roles (admin, instructor, student) |
| `ActivityTypeSeeder.php` | Activity types, question types, assignment types |
| `GradeLevelSeeder.php` | Grade levels (Year 1-5, Grade 1-12) |

## 🔄 Re-seeding

To completely reset and reseed:

```bash
# WARNING: This will DELETE all data!
php artisan migrate:fresh --seed
```

To add more data without resetting:

```bash
# Safe - will update or create new records
php artisan db:seed
```

## ✅ Verification

After seeding, verify the data:

```bash
# Check user counts
php artisan tinker
>>> User::count()
>>> Course::count()
>>> Module::count()

# Or query specific data
>>> User::where('email', 'admin1@test.com')->first()
>>> Course::with('modules.activities')->get()
```

## 🎓 Testing

The seeded data is perfect for:
- Development and testing
- Demo presentations
- User acceptance testing
- Integration testing
- Manual QA testing

Login as different user types to test role-specific features:
- **Admin**: Full system access
- **Instructor**: Course management, student grading
- **Student**: Course enrollment, quiz taking

## 📞 Support

For issues with seeding:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Run with verbose output: `php artisan db:seed --verbose`
3. Check database connection: `php artisan migrate:status`
4. Verify environment: `.env` file configuration

---

**Last Updated**: October 2025
**Laravel Version**: 11.x
**Database**: SQLite (development), PostgreSQL/MySQL (production ready)
