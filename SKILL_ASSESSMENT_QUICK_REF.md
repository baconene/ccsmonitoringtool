# Skill Assessment System - Quick Reference
**Last Updated:** March 4, 2026
**Version:** 1.0
---
## Quick Overview
The Skill Assessment System tracks student mastery of specific learning outcomes (skills) using a weighted scoring system that combines:
- **Activities** (weight: 1.0) - Quizzes, assignments, assessments
- **Lessons** (weight: 0.5) - Completed lessons
---
## Key Metrics
| Metric | Range | Description |
|--------|-------|-------------|
| **Final Score** | 0-100% | Overall skill performance |
| **Normalized Score** | 0-100% | Weighted average of components |
| **Mastery Level** | enum | exceeds / meets / approaching / not_met |
| **Consistency Score** | 0-100% | Performance consistency across components |
| **Attempt Count** | 0+ | Total attempts across skill activities |
---
## Mastery Levels
```
┌─────────────┬──────────────────────┬───────────────────┐
│   Level     │      Condition       │  Example (70%)    │
├─────────────┼──────────────────────┼───────────────────┤
│ exceeds     │ score >= threshold+15│ score >= 85%      │
│ meets       │ score >= threshold   │ 70% ≤ score < 85% │
│ approaching │ score >= threshold-15│ 55% ≤ score < 70% │
│ not_met     │ score < threshold-15 │ score < 55%       │
└─────────────┴──────────────────────┴───────────────────┘
```
---
## Automated Workflows
### Auto-Linking Scenarios
**Scenario 1: Add activity to module**
```
Module has Skills → Activity auto-links to all module skills (weight: 1.0)
```
**Scenario 2: Create skill in module**
```
Module has Activities → Skill auto-links to all module activities (weight: 1.0)
```
### Enrollment Initialization
```
Student enrolls → StudentSkillAssessment created for each module skill
Default values: final_score=0, mastery_level='not_met'
```
### Real-Time Updates
```
Activity completed → calculateStudentAssessment() → Updates all related skills
Lesson completed → calculateStudentAssessment() → Updates all module skills
```
---
## Score Calculation Formula
```
Step 1: Normalized Score
─────────────────────────
Normalized = Σ(component_score × weight) / Σ(weights)
Step 2: Final Score
─────────────────────────
Final = Normalized
      + (feedback_bonus × 0.1)
      + (peer_review_bonus × 0.1)
      + (improvement_factor - 1) × 10
      - late_penalty
Step 3: Clamp
─────────────────────────
Final = max(0, min(100, Final))
```
---
## Quick Examples
### Example 1: Basic Calculation
**Given:**
- Quiz 1: 85% (weight: 1.0)
- Lesson 1: Completed (weight: 0.5)
**Calculation:**
```
Normalized = (85×1.0 + 100×0.5) / (1.0 + 0.5)
           = (85 + 50) / 1.5
           = 90%
```
---
### Example 2: Mixed Components
**Given:**
- Quiz 1: 80% (weight: 1.0)
- Quiz 2: 90% (weight: 1.0)
- Assignment: 75% (weight: 1.0)
- Lesson 1: Completed (weight: 0.5)
- Lesson 2: Completed (weight: 0.5)
**Calculation:**
```
Sum of weighted scores = 80 + 90 + 75 + 50 + 50 = 345
Sum of weights = 1.0 + 1.0 + 1.0 + 0.5 + 0.5 = 4.0
Normalized = 345 / 4.0 = 86.25%
Final = 86.25% (assuming no bonuses/penalties)
Mastery (threshold=70%): 86.25% >= 85% → "exceeds"
```
---
## Database Tables
### `skills`
```sql
- id
- module_id         → Foreign key to modules
- name              → Skill name
- description
- competency_threshold → Default: 70.00
```
### `activity_skill` (Pivot)
```sql
- activity_id       → Foreign key to activities
- skill_id          → Foreign key to skills
- weight            → Default: 1.00
```
### `student_skill_assessments`
```sql
- student_id        → Foreign key to students
- skill_id          → Foreign key to skills
- final_score       → 0-100%
- mastery_level     → enum: exceeds/meets/approaching/not_met
- assessment_metadata → JSON: {activity_count, lesson_count, ...}
```
---
## Common Operations
### Create Skill
```php
$skill = Skill::create([
    'module_id' => $moduleId,
    'name' => 'Critical Thinking',
    'description' => 'Analyze and evaluate information',
    'competency_threshold' => 70
]);
// Auto-links to all module activities
```
### Get Student Skill Assessments
```php
$assessments = StudentSkillAssessment::where('student_id', $studentId)
    ->with('skill')
    ->get();
foreach ($assessments as $assessment) {
    echo "{$assessment->skill->name}: {$assessment->final_score}% ({$assessment->mastery_level})\n";
}
```
### Recalculate Assessment
```php
use App\Services\StudentAssessmentService;
$service = new StudentAssessmentService();
$service->calculateStudentAssessment($student);
```
### Get Skills by Mastery Level
```php
// Students struggling with skills
$struggling = StudentSkillAssessment::where('student_id', $studentId)
    ->whereIn('mastery_level', ['not_met', 'approaching'])
    ->with('skill')
    ->get();
```
---
##  Best Practices
### For Instructors
 **Define 3-5 skills per module** (not too many)
 **Set thresholds at 70-80%** for most skills
 **Review "not_met" students weekly**
 **Balance activities and lessons** (~2:1 ratio)
 **Align skills with learning objectives**
 Avoid creating too many skills (causes cognitive overload)
 Don't set thresholds too high (>90% unrealistic)
 Don't ignore skill data (defeats the purpose)
---
### For Developers
 **Use service layer** for calculations
 **Trigger recalculation** after score changes
 **Use transactions** for skill creation
 **Index on mastery_level** for filtering
 **Validate thresholds** (0-100 range)
 Never update student_skill_assessments directly
 Don't skip auto-linking logic
 Don't hardcode weights
---
### For Students
 **Check skill assessments regularly**
 **Focus on "not_met" skills first**
 **Complete lessons for easy progress** (100% score @ 0.5 weight)
 **Retake quizzes to improve**
 **Aim for "meets" or "exceeds"**
---
##  Troubleshooting
### Problem: Skill assessment = 0 after completing activities
**Check:**
1. Is activity linked to skill?
   ```sql
   SELECT * FROM activity_skill
   WHERE activity_id = ? AND skill_id = ?
   ```
2. Is student progress recorded?
   ```sql
   SELECT * FROM student_activities
   WHERE student_id = ? AND activity_id = ?
   ```
**Fix:** Re-add activity to module or manually link skill
---
### Problem: Auto-linking not working
**Likely Cause:** Observer fired before relationships established
**Fix:** Auto-linking now in controllers (ModuleController, SkillManagementController)
**Verify:**
```php
// After adding activity to module
$module = Module::find($moduleId);
$activity = Activity::find($activityId);
$linked = $activity->skills->contains($module->skills->first()->id);
echo $linked ? "Linked!" : "Not linked";
```
---
### Problem: Mastery level not updating
**Check:**
1. Recalculation triggered?
2. Threshold set correctly?
3. Score actually changed?
**Manual Trigger:**
```php
$service = app(StudentAssessmentService::class);
$service->calculateStudentAssessment($student);
```
---
##  API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/modules/{id}/skills` | List module skills |
| POST | `/api/modules/{id}/skills` | Create skill |
| PUT | `/api/skills/{id}` | Update skill |
| DELETE | `/api/skills/{id}` | Delete skill |
| GET | `/api/students/{id}/skill-assessments` | Get student assessments |
| GET | `/api/skills/{skillId}/assessment/{studentId}` | Get specific assessment |
---
##  Testing Commands
```bash
# Test basic skill assessment flow
php test_skill_assessment_flow.php
# Test enrollment initialization
php test_enrollment_skill_init.php
# Test lesson completion integration
php test_lesson_skill_assessment.php
# Test combined activities + lessons
php test_combined_assessment.php
```
---
##  Related Documentation
- **Full Documentation:** `SKILL_ASSESSMENT_SYSTEM.md`
- **System Overview:** `SYSTEM_DOCUMENTATION.md` → Skill Assessment System
- **Documentation Hub:** `DOCUMENTATION_INDEX.md`
---
## ️ Configuration
### Default Values
```php
// Skill defaults
competency_threshold: 70.00
// Activity-skill link defaults
weight: 1.00
// Lesson weight (hardcoded)
lesson_weight: 0.50
// Mastery level thresholds
exceeds: threshold + 15
meets: threshold
approaching: threshold - 15
not_met: < threshold - 15
```
---
##  Quick Start Guide
### 1️⃣ Create Skills in Module
```
Navigate to Module → Skills → Add Skill
Set: name, description, threshold (default: 70%)
```
### 2️⃣ Add Activities to Module
```
Activities auto-link to module skills (weight: 1.0)
```
### 3️⃣ Enroll Students
```
StudentSkillAssessment records auto-created (score: 0, mastery: not_met)
```
### 4️⃣ Students Complete Work
```
Complete lessons → 100% score @ 0.5 weight
Complete activities → actual score @ 1.0 weight
Assessments update in real-time
```
### 5️⃣ Monitor Progress
```
View student skill assessments
Filter by mastery level
Identify struggling students (not_met/approaching)
```
---
**Support:** support@astrolearn.edu
**Documentation:** `/documentation` in app
**Version:** 1.0 (March 2026)
