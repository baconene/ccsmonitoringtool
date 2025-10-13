# Student Quiz Progress & Answer Duplicate Prevention Fix

## Date
October 13, 2025

## Issues Fixed

### 1. **Multiple StudentQuizProgress Records**
**Problem:** Students could have multiple progress records for the same quiz/activity combination, causing data inconsistency and confusion.

**Root Cause:** 
- No unique constraint at database level
- Controllers used `create()` instead of `updateOrCreate()`
- Multiple quiz start attempts created duplicate records

**Solution:**
- Added unique composite index on `student_quiz_progress` table: `(student_id, quiz_id, activity_id)`
- Updated all controllers to use `updateOrCreate()` instead of `create()`
- Migration automatically removes duplicates before adding constraint

### 2. **Multiple StudentQuizAnswer Records**
**Problem:** Students could have multiple answer records for the same question in a quiz progress session.

**Root Cause:**
- No unique constraint at database level
- Controller checked for duplicates but didn't enforce at DB level
- Race conditions could create duplicates

**Solution:**
- Added unique composite index on `student_quiz_answers` table: `(quiz_progress_id, question_id)`
- Controller already used proper logic, but now enforced at DB level
- Migration automatically removes duplicates before adding constraint

### 3. **answer_text Not Populated for Multiple Choice**
**Problem:** When students selected an option, the `answer_text` field remained null, making historical review difficult if options were later changed.

**Root Cause:**
- Controller only stored `selected_option_id`
- No automatic population of `answer_text` from option text
- Historical data could be lost if options were edited

**Solution:**
- Updated `StudentQuizAnswer` model with boot method to auto-populate `answer_text`
- Updated controller to explicitly set `answer_text` from selected option
- Migration backfills existing records with missing `answer_text`

## Database Changes

### Migration: `2025_10_13_033530_add_unique_constraints_to_student_quiz_tables.php`

**Actions Performed:**

1. **Cleanup Duplicate Progress Records**
   ```sql
   DELETE FROM student_quiz_progress
   WHERE id NOT IN (
       SELECT MAX(id)
       FROM student_quiz_progress
       GROUP BY student_id, quiz_id, activity_id
   )
   ```
   - Keeps the most recent record (highest ID) for each unique combination
   - Removes all older duplicate records

2. **Cleanup Duplicate Answer Records**
   ```sql
   DELETE FROM student_quiz_answers
   WHERE id NOT IN (
       SELECT MAX(id)
       FROM student_quiz_answers
       GROUP BY quiz_progress_id, question_id
   )
   ```
   - Keeps the most recent answer for each question in a progress session

3. **Backfill Missing answer_text**
   ```sql
   UPDATE student_quiz_answers
   SET answer_text = (
       SELECT option_text
       FROM question_options
       WHERE question_options.id = student_quiz_answers.selected_option_id
   )
   WHERE selected_option_id IS NOT NULL
   AND (answer_text IS NULL OR answer_text = '')
   ```
   - Populates `answer_text` from selected option for historical records

4. **Add Unique Constraints**
   - `student_quiz_progress`: unique index on `(student_id, quiz_id, activity_id)`
   - `student_quiz_answers`: unique index on `(quiz_progress_id, question_id)`

## Code Changes

### StudentQuizProgress Model
**File:** `app/Models/StudentQuizProgress.php`

**Changes:** None needed - model was already properly structured

### StudentQuizAnswer Model
**File:** `app/Models/StudentQuizAnswer.php`

**Changes:**
- Added `boot()` method with `saving` event listener
- Automatically populates `answer_text` from `selected_option_id` when saving
- Only populates if `answer_text` is empty

```php
protected static function boot()
{
    parent::boot();

    static::saving(function ($answer) {
        if ($answer->selected_option_id && empty($answer->answer_text)) {
            $option = QuestionOption::find($answer->selected_option_id);
            if ($option) {
                $answer->answer_text = $option->option_text;
            }
        }
    });
}
```

### StudentQuizController
**File:** `app/Http/Controllers/Student/StudentQuizController.php`

**Changes:**

1. **start() method** - Line 62
   ```php
   // Before:
   $progress = StudentQuizProgress::create([...]);
   
   // After:
   $progress = StudentQuizProgress::updateOrCreate(
       ['student_id' => $student->id, 'quiz_id' => $quiz->id, 'activity_id' => $activityId],
       [...]
   );
   ```

2. **submitAnswer() method** - Lines 125-145
   ```php
   // Before: Check existing, then create or update separately
   
   // After: Get answer_text from option, then use updateOrCreate
   $answerText = $validated['answer_text'] ?? null;
   
   if (isset($validated['selected_option_id']) && $validated['selected_option_id']) {
       $selectedOption = \App\Models\QuestionOption::find($validated['selected_option_id']);
       if ($selectedOption) {
           $answerText = $selectedOption->option_text;
       }
   }
   
   StudentQuizAnswer::updateOrCreate(
       ['quiz_progress_id' => $progress->id, 'question_id' => $validated['question_id']],
       ['student_id' => $student->id, 'selected_option_id' => ..., 'answer_text' => $answerText, ...]
   );
   ```

### StudentActivityController
**File:** `app/Http/Controllers/Student/StudentActivityController.php`

**Changes:**

**markComplete() method** - Lines 58-73
```php
// Before:
if (!$existingProgress) {
    StudentQuizProgress::create([...]);
}

// After:
StudentQuizProgress::updateOrCreate(
    ['student_id' => $student->id, 'quiz_id' => $quiz->id, 'activity_id' => $activity->id],
    [...]
);
```

## Data Integrity Guarantees

### Before Fix
❌ Multiple quiz progress records per student per quiz
❌ Multiple answers per question per quiz attempt
❌ Missing historical answer text
❌ Data inconsistency issues
❌ Difficult to track student progress accurately

### After Fix
✅ **Exactly one** quiz progress record per `(student_id, quiz_id, activity_id)`
✅ **Exactly one** answer per `(quiz_progress_id, question_id)`
✅ `answer_text` always populated from selected option
✅ Historical data preserved even if options change
✅ Database-level enforcement prevents future duplicates
✅ Application code uses `updateOrCreate()` for safety

## Testing Scenarios

### Test Case 1: Start Quiz Multiple Times
**Action:** Student starts the same quiz multiple times
**Expected:** Only one progress record exists, gets updated on subsequent starts
**Status:** ✅ Fixed

### Test Case 2: Answer Same Question Multiple Times
**Action:** Student changes their answer for a question
**Expected:** Existing answer is updated, no duplicate created
**Status:** ✅ Fixed

### Test Case 3: Select Multiple Choice Option
**Action:** Student selects option "A) Paris"
**Expected:** 
- `selected_option_id` = ID of "A) Paris" option
- `answer_text` = "A) Paris"
**Status:** ✅ Fixed

### Test Case 4: Migration with Existing Duplicates
**Action:** Run migration on database with duplicate records
**Expected:** 
- Duplicates removed (keeps most recent)
- Unique constraints added successfully
- No data loss for latest records
**Status:** ✅ Tested and working

### Test Case 5: Historical Data
**Action:** Check old quiz answers before the fix
**Expected:** `answer_text` backfilled from `selected_option_id`
**Status:** ✅ Fixed via migration

## Performance Impact

### Database Indexes Added
```sql
CREATE UNIQUE INDEX unique_student_quiz_activity 
ON student_quiz_progress (student_id, quiz_id, activity_id);

CREATE UNIQUE INDEX unique_progress_question 
ON student_quiz_answers (quiz_progress_id, question_id);
```

**Benefits:**
- Faster lookups for existing progress/answers
- Database enforces uniqueness (no duplicate checks needed in code)
- Query performance improved for JOIN operations

**Overhead:**
- Minimal - indexes are small (3 columns and 2 columns respectively)
- Insert/update slightly slower (negligible for user-facing operations)
- Overall performance gain due to reduced duplicate checks

## Rollback Instructions

If issues arise, rollback the migration:

```bash
php artisan migrate:rollback --step=1
```

This will:
1. Remove unique constraints
2. Restore ability to create duplicates (not recommended)
3. Keep all data intact (no data loss)

To re-apply after rollback:
```bash
php artisan migrate
```

## Future Considerations

### Preventing Option Text Changes
Consider adding an audit log or version history for `question_options` to track changes to option text over time. This would allow:
- Viewing historical option text as it was when student answered
- Tracking instructor edits to questions/options
- Better dispute resolution

### Composite Unique Keys
Current implementation uses separate unique constraints. Consider if composite primary keys would be more appropriate:
- `student_quiz_progress`: `(student_id, quiz_id, activity_id)` as primary key
- `student_quiz_answers`: `(quiz_progress_id, question_id)` as primary key

**Pros:** Slightly better performance, clearer semantic meaning
**Cons:** Harder to reference in other tables, more complex foreign keys

**Decision:** Keep current design with auto-increment ID as primary key and unique constraints for flexibility.

## Related Files

### Models
- `app/Models/StudentQuizProgress.php` - Progress tracking model
- `app/Models/StudentQuizAnswer.php` - Answer storage model (updated)
- `app/Models/QuestionOption.php` - Option text source

### Controllers
- `app/Http/Controllers/Student/StudentQuizController.php` - Main quiz logic (updated)
- `app/Http/Controllers/Student/StudentActivityController.php` - Activity completion (updated)

### Migrations
- `database/migrations/2025_10_05_164748_create_student_quiz_progress_table.php` - Original table
- `database/migrations/2025_10_05_164758_create_student_quiz_answers_table.php` - Original table
- `database/migrations/2025_10_13_033530_add_unique_constraints_to_student_quiz_tables.php` - **NEW** - Adds constraints

## Summary

This fix ensures data integrity for student quiz attempts by:
1. ✅ Preventing duplicate progress records at database level
2. ✅ Preventing duplicate answer records at database level
3. ✅ Always populating answer_text for historical preservation
4. ✅ Using updateOrCreate() in all controllers for safety
5. ✅ Automatic cleanup of existing duplicates
6. ✅ Backfilling missing answer_text from options

Students can now safely:
- Start quizzes multiple times without creating duplicates
- Change their answers without creating duplicate records
- Have their answer text preserved even if instructors edit options
- Get consistent, accurate quiz history and results
