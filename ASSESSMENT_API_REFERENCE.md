# Student Assessment System - API Reference

## Base URL
```
http://localhost:8000/api
```

## Authentication
All endpoints require Sanctum authentication:
```
Authorization: Bearer {SANCTUM_TOKEN}
```

## Response Format

All responses are JSON with this structure:

**Success:**
```json
{
  "data": { /* response data */ }
}
```

**Error:**
```json
{
  "message": "Error description",
  "error": "Detailed error message"
}
```

---

## Student Endpoints

### 1. Get Comprehensive Assessment
**Endpoint:** `GET /student/assessment`

**Authentication:** Required (Sanctum)

**Description:** Retrieves complete assessment for authenticated student including overall score, course breakdown, strengths, weaknesses, and radar chart data.

**Response (200):**
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
              },
              {
                "skill_id": 2,
                "skill_name": "Problem Solving",
                "score": 89.0,
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
      },
      {
        "skill_name": "Basic Arithmetic",
        "score": 93.0,
        "difficulty": "basic",
        "tags": ["computation", "fundamentals"]
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
          "Focus on fundamentals - Consider revisiting introductory materials",
          "Multiple attempts without improvement - Try a different learning approach"
        ]
      }
    ],
    "radar_chart": {
      "labels": ["Algebra", "Geometry", "Statistics", "Calculus"],
      "datasets": [
        {
          "label": "Performance",
          "data": [92, 88, 75, 84],
          "borderColor": "rgba(59, 130, 246, 1)",
          "backgroundColor": "rgba(59, 130, 246, 0.2)",
          "borderWidth": 2
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

**Errors:**
- `500` - Failed to retrieve assessment

---

### 2. Get Skill Assessments
**Endpoint:** `GET /student/skills/assessments`

**Authentication:** Required

**Description:** Retrieves all skill assessments for the authenticated student, sorted by score (highest first).

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "skill_id": 1,
      "skill_name": "Basic Arithmetic",
      "normalized_score": 95.0,
      "final_score": 95.0,
      "mastery_level": "exceeds",
      "status": "Exceeds Expectations",
      "consistency_score": 98.5,
      "attempt_count": 1,
      "improvement_factor": 1.0,
      "days_late": 0
    },
    {
      "id": 2,
      "skill_id": 2,
      "skill_name": "Problem Solving",
      "normalized_score": 89.0,
      "final_score": 87.5,
      "mastery_level": "met",
      "status": "Met",
      "consistency_score": 92.3,
      "attempt_count": 2,
      "improvement_factor": 1.05,
      "days_late": 0
    }
  ],
  "count": 2
}
```

**Errors:**
- `500` - Failed to retrieve skill assessments

---

### 3. Get Strengths
**Endpoint:** `GET /student/strengths`

**Authentication:** Required

**Description:** Retrieves identified strength areas (top 20% of skills by score).

**Response (200):**
```json
{
  "data": [
    {
      "skill_name": "Problem Solving",
      "score": 94.5,
      "difficulty": "intermediate",
      "tags": ["critical thinking", "application"]
    },
    {
      "skill_name": "Critical Analysis",
      "score": 91.3,
      "difficulty": "advanced",
      "tags": ["analysis", "synthesis"]
    }
  ],
  "count": 2
}
```

**Errors:**
- `500` - Failed to retrieve strengths

---

### 4. Get Weaknesses
**Endpoint:** `GET /student/weaknesses`

**Authentication:** Required

**Description:** Retrieves identified weakness areas (skills below competency threshold) with gap analysis and recommendations.

**Response (200):**
```json
{
  "data": [
    {
      "skill_name": "Data Interpretation",
      "score": 65.0,
      "threshold": 70.0,
      "gap": 5.0,
      "difficulty": "advanced",
      "recommendations": [
        "Focus on fundamentals - Consider revisiting introductory materials",
        "Consider strengthening prerequisite skills first"
      ]
    },
    {
      "skill_name": "Statistical Analysis",
      "score": 58.0,
      "threshold": 75.0,
      "gap": 17.0,
      "difficulty": "advanced",
      "recommendations": [
        "Focus on fundamentals - Consider revisiting introductory materials",
        "Multiple attempts without improvement - Try a different learning approach"
      ]
    }
  ],
  "count": 2
}
```

**Errors:**
- `500` - Failed to retrieve weaknesses

---

### 5. Get Radar Chart Data
**Endpoint:** `GET /student/assessment/radar`

**Authentication:** Required

**Description:** Retrieves pre-formatted radar chart data for module performance visualization.

**Response (200):**
```json
{
  "data": {
    "labels": ["Algebra", "Geometry", "Statistics", "Calculus", "Linear Algebra"],
    "datasets": [
      {
        "label": "Performance",
        "data": [92, 88, 75, 84, 80],
        "borderColor": "rgba(59, 130, 246, 1)",
        "backgroundColor": "rgba(59, 130, 246, 0.2)",
        "borderWidth": 2
      }
    ]
  }
}
```

**Errors:**
- `500` - Failed to retrieve radar data

---

## Admin Endpoints

### 6. Get Student Assessment (Admin)
**Endpoint:** `GET /admin/student/{studentId}/assessment`

**Authentication:** Required + Verified User

**Parameters:**
- `studentId` (path) - ID of the student to assess

**Description:** Retrieves assessment for a specific student (admin/instructor only).

**Response (200):** Same as endpoint #1

**Errors:**
- `404` - Student not found
- `500` - Assessment calculation failed

---

### 7. Recalculate Course Assessments
**Endpoint:** `POST /admin/course/{courseId}/recalculate-assessments`

**Authentication:** Required + Verified User

**Parameters:**
- `courseId` (path) - ID of the course

**Description:** Recalculates and updates assessments for all students in a course.

**Response (200):**
```json
{
  "message": "Recalculated assessments for 25 students",
  "count": 25
}
```

**Errors:**
- `500` - Recalculation failed

---

### 8. Compare Student Assessments
**Endpoint:** `POST /admin/assessment/compare`

**Authentication:** Required + Verified User

**Request Body:**
```json
{
  "student_ids": [1, 2, 3, 4, 5]
}
```

**Validation:**
- `student_ids` (required, array, min 2 items)
- `student_ids.*` (integer)

**Description:** Retrieves and compares assessments of multiple students.

**Response (200):**
```json
{
  "data": [
    {
      "student_id": 1,
      "student_name": "John Doe",
      "overall_score": 82.5,
      "readiness_level": "Proficient",
      ...
    },
    {
      "student_id": 2,
      "student_name": "Jane Smith",
      "overall_score": 88.0,
      "readiness_level": "Advanced",
      ...
    }
  ],
  "count": 2
}
```

**Errors:**
- `422` - Validation error (wrong data format)
- `500` - Comparison failed

---

## Data Types & Enums

### Readiness Levels
```
"Not Ready"    - Score < 50%
"Developing"   - Score 50-70%
"Proficient"   - Score 70-85%
"Advanced"     - Score ≥ 85%
```

### Mastery Levels
```
"not_met"      - Below competency threshold
"met"          - At competency threshold
"exceeds"      - 15% above competency threshold
```

### Difficulty Levels
```
"basic"        - Elementary concepts
"intermediate" - Standard competency
"advanced"     - Higher-order thinking
"expert"       - Mastery-level expertise
```

### Bloom's Taxonomy Levels
```
"remember"     - Recall knowledge
"understand"   - Comprehend concepts
"apply"        - Use information
"analyze"      - Break down materials
"evaluate"     - Make judgments
"create"       - Put elements together
```

---

## Status Codes

| Code | Meaning | When |
|------|---------|------|
| 200 | OK | Successful request |
| 422 | Unprocessable Entity | Validation error |
| 404 | Not Found | Resource doesn't exist |
| 500 | Server Error | Processing failed |

---

## Score Calculations

### Score Range
```
0-100    All scores normalized to percentage
```

### Mastery Determination
```
Default Threshold: 70%

score < 70%:           not_met
70% ≤ score < 85%:     met
score ≥ 85%:           exceeds (threshold + 15%)
```

### Adjustment Factors
```
Feedback Score:        +10% weight maximum
Peer Review Score:     +10% weight maximum
Improvement Factor:    Based on attempt count
  - Single attempt: 1.0
  - Multiple: 1.0 + (log(attempts) × 0.035)
Late Penalty:          -2% per day past due
```

---

## Rate Limiting

No rate limiting currently implemented. Consider adding:
```php
// In middleware
protected $except = [
    'api/student/*',
    'api/admin/*'
];
```

---

## Caching Strategy

For performance, consider caching assessments:
```php
// Cache assessment for 1 hour
cache()->remember("student_assessment_{$studentId}", 3600, fn() => $assessment);

// Invalidate on activity completion
cache()->forget("student_assessment_{$studentId}");
```

---

## Error Handling

All errors follow this format:
```json
{
  "message": "Human-readable error message",
  "error": "Technical error details"
}
```

**Common Errors:**

```json
{
  "message": "Failed to retrieve assessment",
  "error": "Call to undefined method Module::skills()"
}
```

**Solution:** Ensure all migrations ran and models have relationships.

---

## Example Requests

### cURL
```bash
# Get assessment
curl http://localhost:8000/api/student/assessment \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Compare students (admin)
curl -X POST http://localhost:8000/api/admin/assessment/compare \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"student_ids": [1, 2, 3]}'
```

### JavaScript/Axios
```javascript
// Get assessment
const response = await axios.get('/api/student/assessment');
console.log(response.data.data);

// Get strengths
const strengths = await axios.get('/api/student/strengths');
console.log(strengths.data.data);

// Compare students
const comparison = await axios.post('/api/admin/assessment/compare', {
  student_ids: [1, 2, 3, 4, 5]
});
console.log(comparison.data.data);
```

### Vue 3 Composition API
```typescript
import { ref } from 'vue'
import axios from 'axios'

const assessment = ref(null)
const isLoading = ref(false)

const fetchAssessment = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/student/assessment')
    assessment.value = response.data.data
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchAssessment()
})
```

---

## Webhook Events (Future)

Consider implementing webhooks for:
```
assessment.created
assessment.updated
strength.identified
weakness.identified
mastery.achieved
```

---

## Documentation Links

- [Full Implementation Guide](./STUDENT_ASSESSMENT_SYSTEM.md)
- [Quick Start Guide](./ASSESSMENT_QUICK_START.md)
- [Database Schema](./STUDENT_ASSESSMENT_SYSTEM.md#database-schema)
- [Service Logic](./STUDENT_ASSESSMENT_SYSTEM.md#backend-services)

---

**API Version:** 1.0.0  
**Last Updated:** February 20, 2026  
**Spec Compliance:** RESTful JSON API
