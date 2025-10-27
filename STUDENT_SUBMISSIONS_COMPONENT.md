# Student Submissions Component

A reusable Vue component for displaying student activity progress, scores, and submissions across different activity types (Assignments, Quizzes, and Projects).

## Features

- ✅ **Unified Interface**: Single component works with all activity types
- ✅ **Real-time Statistics**: Shows submission rate, average score, graded count
- ✅ **Status Tracking**: Visual indicators for different submission statuses
- ✅ **Progress Visualization**: Color-coded progress bars
- ✅ **Quick Actions**: View button to navigate to detailed submission view
- ✅ **Responsive Design**: Mobile-friendly table layout
- ✅ **Type-safe**: Full TypeScript support

## Component Location

```
resources/js/components/StudentSubmissions.vue
```

## Composable Helper

```
resources/js/composables/useStudentSubmissions.ts
```

## Usage Examples

### 1. Assignment Management

```vue
<script setup lang="ts">
import StudentSubmissions from '@/components/StudentSubmissions.vue';
import { useStudentSubmissions } from '@/composables/useStudentSubmissions';
import { onMounted } from 'vue';

const props = defineProps<{
    assignment: {
        id: number;
        title: string;
        course_id: number;
    };
}>();

const { submissions, loading, fetchAssignmentSubmissions } = useStudentSubmissions();

onMounted(() => {
    fetchAssignmentSubmissions(props.assignment.id);
});
</script>

<template>
    <StudentSubmissions
        :submissions="submissions"
        :activity-type="'assignment'"
        :activity-id="assignment.id"
        :activity-title="assignment.title"
        :course-id="assignment.course_id"
        :loading="loading"
    />
</template>
```

### 2. Quiz Management

```vue
<script setup lang="ts">
import StudentSubmissions from '@/components/StudentSubmissions.vue';
import { useStudentSubmissions } from '@/composables/useStudentSubmissions';
import { onMounted } from 'vue';

const props = defineProps<{
    quiz: {
        id: number;
        title: string;
    };
}>();

const { submissions, loading, fetchQuizSubmissions } = useStudentSubmissions();

onMounted(() => {
    fetchQuizSubmissions(props.quiz.id);
});
</script>

<template>
    <StudentSubmissions
        :submissions="submissions"
        :activity-type="'quiz'"
        :activity-id="quiz.id"
        :activity-title="quiz.title"
        :loading="loading"
    />
</template>
```

### 3. Project Management

```vue
<script setup lang="ts">
import StudentSubmissions from '@/components/StudentSubmissions.vue';
import { useStudentSubmissions } from '@/composables/useStudentSubmissions';
import { onMounted } from 'vue';

const props = defineProps<{
    project: {
        id: number;
        title: string;
        course_id: number;
    };
}>();

const { submissions, loading, fetchProjectSubmissions } = useStudentSubmissions();

onMounted(() => {
    fetchProjectSubmissions(props.project.id);
});
</script>

<template>
    <StudentSubmissions
        :submissions="submissions"
        :activity-type="'project'"
        :activity-id="project.id"
        :activity-title="project.title"
        :course-id="project.course_id"
        :loading="loading"
    />
</template>
```

## Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `submissions` | `StudentSubmission[]` | Yes | Array of student submission data |
| `activityType` | `'assignment' \| 'quiz' \| 'project'` | Yes | Type of activity |
| `activityId` | `number` | Yes | ID of the activity |
| `activityTitle` | `string` | Yes | Title of the activity |
| `courseId` | `number` | No | ID of the course (optional) |
| `loading` | `boolean` | No | Loading state (default: false) |

## StudentSubmission Interface

```typescript
interface StudentSubmission {
    id: number;                    // Submission ID
    student_id: number;            // Student ID
    student_name: string;          // Student full name
    progress: number;              // 0-100 percentage
    score: number | null;          // Student's score
    total_score?: number;          // Maximum possible score
    status: 'not_started' | 'in_progress' | 'submitted' | 'graded' | 'late';
    submitted_at?: string;         // ISO date string
    graded_at?: string;            // ISO date string
}
```

## Status Types

| Status | Description | Badge Color | Icon |
|--------|-------------|-------------|------|
| `not_started` | Student hasn't begun | Gray | Clock |
| `in_progress` | Currently working on it | Blue | AlertCircle |
| `submitted` | Submitted, awaiting grading | Yellow | CheckCircle2 |
| `graded` | Graded and finalized | Green | CheckCircle2 |
| `late` | Submitted after deadline | Red | XCircle |

## Navigation Routes

The component automatically generates the correct route based on activity type:

### Assignment
```
/instructor/assignments/{activityId}/submissions/{submissionId}
```

### Quiz
```
/instructor/activities/{activityId}/quiz-results/{studentId}
```

### Project
```
/instructor/projects/{activityId}/submissions/{submissionId}
```

## Statistics Displayed

1. **Total Submitted**: Count of submitted + graded submissions
2. **Total Graded**: Count of graded submissions
3. **Submission Rate**: Percentage of students who submitted
4. **Average Score**: Average score across all graded submissions

## Backend Requirements

### Expected API Endpoints

#### For Assignments:
```
GET /instructor/assignments/{id}/submissions
```

#### For Quizzes:
```
GET /instructor/activities/{id}/quiz-results
```

#### For Projects:
```
GET /instructor/projects/{id}/submissions
```

### Expected Response Format

```json
[
    {
        "id": 1,
        "student_id": 123,
        "student": {
            "name": "John Doe"
        },
        "status": "submitted",
        "score": 85,
        "total_score": 100,
        "submitted_at": "2025-10-26T10:30:00Z",
        "graded_at": null,
        "due_date": "2025-10-25T23:59:00Z"
    }
]
```

## Customization

### Override View Route

You can customize the navigation route by modifying the `getViewRoute` function in the component:

```typescript
const getViewRoute = (submission: StudentSubmission) => {
    // Custom route logic
    return `/custom/route/${submission.id}`;
};
```

### Custom Status Colors

Modify the `statusConfig` object to change badge variants and colors:

```typescript
const statusConfig = {
    not_started: {
        label: 'Not Started',
        variant: 'secondary',
        icon: Clock,
        color: 'text-gray-500',
    },
    // ... other statuses
};
```

## Notification Integration

When a student completes an activity, you can trigger a notification and redirect the instructor to the submissions view:

```php
// In StudentAssignmentController or similar
InstructorNotification::create([
    'instructor_id' => $instructorUserId,
    'type' => 'assignment_submitted',
    'title' => 'New Assignment Submission',
    'message' => "{$student->name} has submitted the assignment \"{$assignment->title}\"",
    'data' => [
        'student_id' => $student->id,
        'assignment_id' => $assignment->id,
        'activity_id' => $activity->id,
        'course_id' => $course->id,
    ],
]);
```

The notification can link directly to the StudentSubmissions component via the activity management page.

## Example: Full Integration in Assignment Management

```vue
<script setup lang="ts">
import { ref } from 'vue';
import StudentSubmissions from '@/components/StudentSubmissions.vue';
import { useStudentSubmissions } from '@/composables/useStudentSubmissions';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const props = defineProps<{
    assignment: {
        id: number;
        title: string;
        course_id: number;
    };
}>();

const activeTab = ref('details');
const { submissions, loading, fetchAssignmentSubmissions } = useStudentSubmissions();

const loadSubmissions = () => {
    fetchAssignmentSubmissions(props.assignment.id);
};

// Load when submissions tab is activated
const onTabChange = (tab: string) => {
    if (tab === 'submissions' && submissions.value.length === 0) {
        loadSubmissions();
    }
};
</script>

<template>
    <div class="space-y-6">
        <Tabs v-model="activeTab" @update:model-value="onTabChange">
            <TabsList>
                <TabsTrigger value="details">Assignment Details</TabsTrigger>
                <TabsTrigger value="submissions">Student Submissions</TabsTrigger>
            </TabsList>

            <TabsContent value="details">
                <!-- Assignment details here -->
            </TabsContent>

            <TabsContent value="submissions">
                <StudentSubmissions
                    :submissions="submissions"
                    activity-type="assignment"
                    :activity-id="assignment.id"
                    :activity-title="assignment.title"
                    :course-id="assignment.course_id"
                    :loading="loading"
                />
            </TabsContent>
        </Tabs>
    </div>
</template>
```

## Testing

### Component Testing

```typescript
import { mount } from '@vue/test-utils';
import StudentSubmissions from '@/components/StudentSubmissions.vue';

describe('StudentSubmissions', () => {
    it('displays submissions correctly', () => {
        const wrapper = mount(StudentSubmissions, {
            props: {
                submissions: [
                    {
                        id: 1,
                        student_id: 1,
                        student_name: 'John Doe',
                        progress: 100,
                        score: 85,
                        total_score: 100,
                        status: 'graded',
                        submitted_at: '2025-10-26T10:00:00Z',
                    },
                ],
                activityType: 'assignment',
                activityId: 1,
                activityTitle: 'Test Assignment',
            },
        });

        expect(wrapper.text()).toContain('John Doe');
        expect(wrapper.text()).toContain('85 / 100');
    });
});
```

## Benefits

1. **Code Reusability**: Single component for all activity types
2. **Consistent UX**: Same interface across assignments, quizzes, and projects
3. **Easy Maintenance**: Update once, applies everywhere
4. **Type Safety**: TypeScript ensures data consistency
5. **Performance**: Lazy loading of submission data
6. **Accessibility**: Screen reader friendly with proper ARIA labels

## Future Enhancements

- [ ] Bulk grading actions
- [ ] Export submissions to CSV
- [ ] Filtering and advanced search
- [ ] Sort by multiple columns
- [ ] Inline score editing
- [ ] Submission comments
- [ ] Real-time updates via WebSockets
