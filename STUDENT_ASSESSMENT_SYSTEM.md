# Student Assessment & Competency Evaluation System - Implementation Guide

## Overview

This document provides a comprehensive overview of the Student Assessment & Competency Evaluation System implemented in the Laravel + Vue 3 Learning Management System. The system evaluates students based on their performance across courses, modules, and activities, providing detailed competency insights through interactive visualizations.

## System Architecture

### Technology Stack

**Backend:**
- Laravel 12
- MySQL Database
- Laravel Sanctum for authentication
- Eloquent ORM with Resource Controllers
- Service Layer Pattern

**Frontend:**
- Vue 3 (Composition API)
- Chart.js for Radar visualization
- Axios for API communication
- TailwindCSS for styling

## Database Schema

### New Tables Created

#### 1. **skills** Table
Defines competency skills associated with modules.

```sql
- id (Primary Key)
- module_id (Foreign Key → modules)
- name (string)
- description (text)
- difficulty_level (enum: basic, intermediate, advanced, expert)
- weight (decimal: 0.00-100.00)
- competency_threshold (decimal: 0.00-100.00)
- bloom_level (enum: remember, understand, apply, analyze, evaluate, create)
- tags (JSON array)
- timestamps
```

#### 2. **student_skill_assessments** Table
Tracks individual student competency in each skill.

```sql
- id (Primary Key)
- student_id (Foreign Key → students)
- skill_id (Foreign Key → skills)
- normalized_score (decimal: 0-100)
- feedback_score (decimal: nullable)
- peer_review_score (decimal: nullable)
- attempt_count (integer)
- improvement_factor (decimal)
- days_late (integer)
- final_score (decimal: 0-100)
- mastery_level (enum: not_met, met, exceeds)
- consistency_score (decimal: 0-100)
- assessment_metadata (JSON)
- timestamps
- Unique Index: (student_id, skill_id)
```

#### 3. **skill_activities** Table
Links skills to activities, establishing the competency framework.

```sql
- id (Primary Key)
- skill_id (Foreign Key → skills)
- activity_id (Foreign Key → activities)
- weight (decimal: default 1.0)
- timestamps
- Unique Index: (skill_id, activity_id)
```

### Updated Relationships

**Student Model:** Added `skillAssessments()` relationship
**Module Model:** Added `skills()` relationship
**Activity Model:** Added `skills()` relationship
**Skill Model:** New model with relationships to Module, Activities, and StudentSkillAssessments

## Evaluation Logic

### Scoring Formula

#### 1. **Normalized Score Calculation**
```
normalized_score = (sum(activity_score × activity_weight)) / total_weight × 100
```

#### 2. **Final Score Calculation**
```
final_score = normalized_score 
            + (feedback_score × 0.1)
            + (peer_review_score × 0.1)
            + (improvement_factor - 1) × 10
            - late_penalty
```

**Late Penalty:** 2% per day past due date (configurable)

**Improvement Factor:** 
- 1 attempt = 1.0
- 2+ attempts: 1.0 + (log(attempt_count) × 0.035)

#### 3. **Mastery Level Determination**

| Final Score | Mastery Level | Condition |
|---|---|---|
| < threshold | Not Met | Below minimum |
| ≥ threshold | Met | At or above threshold |
| ≥ threshold + 15% | Exceeds | 15% above threshold |

Default threshold: 70%

#### 4. **Module Score**
```
module_score = sum(skill_score × skill_weight)
```

#### 5. **Course Score**
```
course_score = sum(module_score × module_weight)
```

#### 6. **Overall Student Score**
```
overall_score = average(all_course_scores)
```

### Readiness Levels

```
Score Range    Level
< 50%          Not Ready
50-70%         Developing
70-85%         Proficient
≥ 85%          Advanced
```

## Core Components

### Backend Services

#### **StudentAssessmentService** (`app/Services/StudentAssessmentService.php`)

Primary service handling all assessment calculations.

**Key Methods:**

1. **calculateStudentAssessment(Student $student)**
   - Comprehensive assessment across all enrolled courses
   - Returns overall score, readiness level, course breakdowns, strengths, weaknesses, and radar chart data

2. **calculateCourseAssessment($student, $course)**
   - Course-level evaluation with module breakdown
   - Calculates weighted module averages

3. **calculateModuleAssessment($student, $module)**
   - Module-level skills assessment
   - Returns skill breakdown with mastery levels

4. **calculateOrUpdateSkillAssessment(Student $student, Skill $skill)**
   - Individual skill competency evaluation
   - Creates/updates StudentSkillAssessment records
   - Applies all adjustment factors

5. **identifyStrengths(Collection $assessments, int $topPercentile = 20)**
   - Identifies top 20% highest-scoring skills
   - Includes performance stability criteria

6. **identifyWeaknesses(Collection $assessments)**
   - Finds skills below competency threshold
   - Generates improvement recommendations

7. **buildRadarChartData($student, array $courseAssessments)**
   - Prepares data for spider/radar visualization
   - Limits to 8 modules for clarity

### API Endpoints

#### Student Endpoints (Authenticated)

```
GET /api/student/assessment
Returns comprehensive assessment with all breakdowns

GET /api/student/skills/assessments
Returns array of skill assessments ordered by score

GET /api/student/strengths
Returns identified strength areas with recommendations

GET /api/student/weaknesses
Returns identified weakness areas with gaps

GET /api/student/assessment/radar
Returns radar chart data for visualization
```

#### Admin Endpoints (Verified Users)

```
GET /api/admin/student/{studentId}/assessment
Returns assessment for specific student

POST /api/admin/course/{courseId}/recalculate-assessments
Recalculates all student assessments in a course

POST /api/admin/assessment/compare
Compares assessments of multiple students
```

### API Resources

**StudentAssessmentResource** - Formats full assessment response
**StudentSkillAssessmentResource** - Formats individual skill assessment

### Models

#### Skill Model
```php
- Relationships:
  - belongsTo Module
  - belongsToMany Activities
  - hasMany StudentSkillAssessments
- Attributes:
  - name, description, difficulty_level, weight
  - competency_threshold, bloom_level, tags
```

#### StudentSkillAssessment Model
```php
- Relationships:
  - belongsTo Student
  - belongsTo Skill
- Methods:
  - isMastered(), isExceeding()
  - getStatusAttribute(), getProgressPercentageAttribute()
```

#### SkillActivity Model
```php
- Relationships:
  - belongsTo Skill
  - belongsTo Activity
- Represents: Many-to-many with weight
```

## Frontend Components

### Pages

#### **MyAssessment.vue** (`resources/js/pages/Student/MyAssessment.vue`)

Main assessment dashboard displaying:

**Sections:**
1. **Overall Score Card** - Overall competency score with readiness level badge
2. **Assessment Summary Stats** - Key metrics and counts
3. **Readiness Legend** - Color-coded level definitions
4. **Performance Radar Chart** - Visual module performance comparison
5. **Strengths Section** - Top performing skills with tags
6. **Areas for Improvement** - Below-threshold skills with gap analysis
7. **Course Breakdown** - Module-level scores with progress bars

**Features:**
- Real-time API data fetching
- Responsive design (mobile-first)
- Dark mode support
- Loading states
- Error handling

### Components

#### **RadarChart.vue** (`resources/js/components/Charts/RadarChart.vue`)

Chart.js-based radar/spider diagram visualization.

**Features:**
- Responsive container
- Semi-transparent fill with gradient
- Interactive tooltips showing percentages
- Dark mode optimization
- Dynamic data updates with watchers

**Props:**
```typescript
{
  labels: string[]      // Module/skill names
  datasets: Array       // Chart datasets with styling
}
```

## State Management

### useAssessment Composable (`resources/js/composables/useAssessment.ts`)

Centralized assessment state and API communication.

**State:**
```typescript
- assessment: Assessment | null
- skillAssessments: SkillAssessment[]
- isLoading: boolean
- error: string | null
- lastUpdated: Date | null
```

**Methods:**
```typescript
- fetchAssessment()
- fetchSkillAssessments()
- fetchStrengths()
- fetchWeaknesses()
- fetchRadarData()
- fetchStudentAssessment(studentId)
- recalculateCourseAssessments(courseId)
- compareStudentAssessments(studentIds[])
- clearAssessment()
```

**Computed Properties:**
```typescript
- hasAssessment
- overallScore
- readinessLevel
- strengths
- weaknesses
- courseCount
- masteredSkills
- skillGaps
- averageConsistency
```

## Seeders

### SkillSeeder (`database/seeders/SkillSeeder.php`)

Creates competency skills for existing modules.

**Features:**
- Intelligent skill templates based on course/module type
- Automatic activity linking to skills
- Weight distribution
- Bloom's taxonomy levels

**Supported Course Types:**
- Mathematics (arithmetic, problem-solving, reasoning)
- Science (knowledge, experimentation, data analysis)
- English (comprehension, writing, critical analysis)
- Default template for other subjects

### StudentSkillAssessmentSeeder (`database/seeders/StudentSkillAssessmentSeeder.php`)

Populates sample student skill assessments.

**Process:**
1. Iterates through enrolled students
2. Calculates assessment for each skill
3. Creates StudentSkillAssessment records
4. Leverages StudentAssessmentService

## Unit Tests

### StudentAssessmentServiceTest (`tests/Unit/Services/StudentAssessmentServiceTest.php`)

Comprehensive test suite covering:

**Test Cases (18 total):**
1. Normalized score calculation
2. Mastery level determination (not_met, met, exceeds)
3. Improvement factor calculations
4. Overall student assessment structure
5. Readiness level determination (all 4 levels)
6. Strength identification
7. Weakness identification
8. Radar chart data generation
9. Empty assessment handling

**Setup:** 
- Factory-based test data creation
- Isolated test environment per test
- Clean database state

## How to Use

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- skills table
- student_skill_assessments table
- skill_activities table

### 2. Seed Sample Data

```bash
php artisan db:seed --class=SkillSeeder
php artisan db:seed --class=StudentSkillAssessmentSeeder
```

### 3. Access in Frontend

Add a route or navigation link to MyAssessment:

```typescript
// In Router
{
  path: '/assessment',
  component: () => import('@/pages/Student/MyAssessment.vue'),
  meta: { requiresAuth: true }
}
```

### 4. Run Tests

```bash
php artisan test tests/Unit/Services/StudentAssessmentServiceTest.php
```

## API Response Example

```json
{
  "data": {
    "student_id": 12,
    "student_name": "John Doe",
    "overall_score": 82.5,
    "readiness_level": "Proficient",
    "assessment_date": "2026-02-20T15:30:00Z",
    "courses": [
      {
        "course_id": 1,
        "course_name": "Mathematics",
        "score": 88.0,
        "modules": [
          {
            "module_id": 1,
            "module_name": "Algebra",
            "score": 92.0,
            "skills": [
              {
                "skill_id": 1,
                "skill_name": "Basic Arithmetic",
                "score": 95.0,
                "mastery_level": "exceeds"
              }
            ]
          }
        ]
      }
    ],
    "strengths": [
      {
        "skill_name": "Problem Solving",
        "score": 94.5,
        "difficulty": "intermediate",
        "tags": ["critical thinking", "application"]
      }
    ],
    "weaknesses": [
      {
        "skill_name": "Data Interpretation",
        "score": 65.0,
        "threshold": 70.0,
        "gap": 5.0,
        "difficulty": "advanced",
        "recommendations": [
          "Focus on fundamentals - Consider revisiting introductory materials"
        ]
      }
    ],
    "radar_chart": {
      "labels": ["Algebra", "Geometry", "Statistics"],
      "datasets": [
        {
          "label": "Performance",
          "data": [92, 88, 75],
          "borderColor": "rgba(59, 130, 246, 1)",
          "backgroundColor": "rgba(59, 130, 246, 0.2)"
        }
      ]
    },
    "summary": {
      "total_courses": 2,
      "strengths_count": 3,
      "weaknesses_count": 2,
      "average_skill_score": 82.5
    }
  }
}
```

## Performance Considerations

### Caching
- Assessment calculations are computed dynamically (no stored computed values)
- Consider adding query caching for repeated requests:
  ```php
  cache()->remember("student_assessment_{$studentId}", 60, fn() => ...);
  ```

### Scalability
- Database indexes on frequently queried fields (student_id, skill_id, mastery_level)
- Activity scoring sub-queries benefit from indexed foreign keys
- Radar chart limits to 8 modules to prevent UI performance degradation

### Future AI Integration
- Predictive modeling can leverage historical assessment data
- Skill gap trends trackable through versioned StudentSkillAssessment records
- Assessment metadata field supports storing ML predictions

## Extension Points

### 1. **Additional Adjustment Factors**
```php
// In StudentAssessmentService
- Attendance impact on scores
- Class participation multiplier
- Self-assessment alignment
```

### 2. **Custom Scoring Algorithms**
```php
// Create alternative scoring strategies
- Weighted percentile ranking
- Norm-referenced scoring
- IRT (Item Response Theory) models
```

### 3. **Advanced Visualizations**
```vue
// Additional Vue components
- Progress Timeline Chart
- Skill Progression Heatmap
- Peer Comparison Visualizations
- Predictive Performance Graphs
```

### 4. **Reporting Features**
```php
// New endpoints/features
- PDF Assessment Report Generation
- Departmental Performance Analytics
- Trend Analysis over academic years
- Comparative cohort analysis
```

### 5. **Student Goal Setting**
```php
// Integration with Student goals/plans
- Target competency goals
- Learning path recommendations
- Progress tracking toward goals
- Automated intervention alerts
```

## Troubleshooting

### Issue: Assessment returns empty/zeros
**Solution:**
1. Verify student is enrolled in courses
2. Confirm modules have skills assigned
3. Check activities are linked to skills
4. Ensure student has completed relevant activities with scores

### Issue: Radar chart not displaying
**Solution:**
1. Check Chart.js is properly installed
2. Verify labels array has values
3. Check datasets format matches Chart.js radar requirements
4. Ensure module data is populated

### Issue: Late penalty not appearing
**Solution:**
1. Verify activity has `due_date` set
2. Check StudentActivity has `completed_at` timestamp
3. Ensure `days_late` calculation logic is correct
4. Review penalty constant: DEFAULT_LATE_PENALTY_PER_DAY

## Future Enhancements

1. **AI-Powered Recommendations** - ML models suggesting personalized learning paths
2. **Peer Comparison** - Benchmark individual against cohort
3. **Portfolio Analytics** - Longitudinal skill development tracking
4. **Adaptive Assessment** - Difficulty adjustment based on performance
5. **Mobile Integration** - Mobile-optimized assessment dashboard
6. **VR/AR Skill Training** - Immersive learning experiences
7. **Competency Certification** - Digital badges and certificates
8. **Integration with Professional Standards** - Alignment with industry competency frameworks

## Support & Maintenance

- Run tests regularly: `php artisan test`
- Monitor database growth: `student_skill_assessments` grows with students × skills × assessments
- Backup skill definitions and assessment history
- Consider archiving old assessments after academic years

---

**Last Updated:** February 20, 2026  
**System Version:** 1.0.0  
**Status:** Production Ready
