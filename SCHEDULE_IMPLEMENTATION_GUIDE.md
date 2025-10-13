# Schedule System Implementation Guide

## Installation Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `schedule_types`
- `schedules`
- `schedule_participants`
- `schedule_activities`
- `schedule_courses`
- `schedule_adhoc`

### 2. Seed Schedule Types

```bash
php artisan db:seed --class=ScheduleTypeSeeder
```

This will populate the `schedule_types` table with:
- Activity (Blue #3B82F6)
- Course (Green #10B981)
- Adhoc (Amber #F59E0B)
- Exam (Red #EF4444)
- Office Hours (Purple #8B5CF6)

### 3. Load API Routes

Add this line to your `bootstrap/app.php` or `routes/web.php`:

```php
// In routes/web.php, add:
require __DIR__.'/api_schedules.php';
```

Or in `bootstrap/app.php` (Laravel 11+):

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/api_schedules.php'));
        }
    )
    // ...
```

## API Endpoints

### 1. Get Upcoming Schedules for a User
```http
GET /api/users/{userId}/schedules/upcoming
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Mathematics Quiz 1",
      "description": "Chapter 1-3 Quiz...",
      "location": "Room 101",
      "from_datetime": "2025-10-15T09:00:00.000000Z",
      "to_datetime": "2025-10-15T10:00:00.000000Z",
      "duration_minutes": 60,
      "is_all_day": false,
      "status": "scheduled",
      "type": {
        "name": "activity",
        "color": "#3B82F6",
        "icon": "clipboard-list"
      },
      "creator": {
        "id": 4,
        "name": "Dr. Instructor 1"
      },
      "user_role": "student",
      "participation_status": "invited",
      "participants": [...],
      "participants_count": 3
    }
  ],
  "count": 5
}
```

### 2. Get Schedules in Date Range (for Calendar)
```http
GET /api/schedules/range?user_id=9&start_date=2025-10-01&end_date=2025-10-31
```

**Response:** (FullCalendar-compatible format)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Mathematics Quiz 1",
      "start": "2025-10-15T09:00:00+00:00",
      "end": "2025-10-15T10:00:00+00:00",
      "allDay": false,
      "backgroundColor": "#3B82F6",
      "borderColor": "#3B82F6",
      "extendedProps": {
        "description": "...",
        "location": "Room 101",
        "type": "activity",
        "icon": "clipboard-list",
        "status": "scheduled",
        "userRole": "student",
        "participationStatus": "invited",
        "participantsCount": 3
      }
    }
  ]
}
```

### 3. Create a New Schedule
```http
POST /api/schedules
```

**Request Body:**
```json
{
  "schedule_type_id": 1,
  "title": "Mathematics Quiz 1",
  "description": "Chapter 1-3 Quiz covering algebra",
  "location": "Room 101",
  "from_datetime": "2025-10-15 09:00:00",
  "to_datetime": "2025-10-15 10:00:00",
  "is_all_day": false,
  "participants": [
    {
      "user_id": 4,
      "role_in_schedule": "instructor",
      "participation_status": "accepted"
    },
    {
      "user_id": 9,
      "role_in_schedule": "student",
      "participation_status": "invited"
    }
  ],
  "schedulable_type": "App\\Models\\Activity",
  "schedulable_id": 1
}
```

### 4. Update Schedule (Drag-and-Drop Rescheduling)
```http
PATCH /api/schedules/{id}
```

**Request Body:**
```json
{
  "from_datetime": "2025-10-16 10:00:00",
  "to_datetime": "2025-10-16 11:00:00"
}
```

### 5. Check for Conflicts
```http
POST /api/schedules/check-conflicts
```

**Request Body:**
```json
{
  "user_id": 9,
  "from_datetime": "2025-10-15 09:30:00",
  "to_datetime": "2025-10-15 10:30:00",
  "exclude_schedule_id": null
}
```

### 6. Update Participant Status
```http
PATCH /api/schedules/{scheduleId}/participants/{userId}/status
```

**Request Body:**
```json
{
  "participation_status": "accepted",
  "notes": "Looking forward to it!"
}
```

## Vue.js Dashboard Integration

### Section 7: Update InstructorDashboard.vue

Add to `resources/js/dashboards/InstructorDashboard.vue`:

```vue
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Calendar, Clock, Users, MapPin } from 'lucide-vue-next';

interface Schedule {
  id: number;
  title: string;
  description: string;
  location: string;
  from_datetime: string;
  to_datetime: string;
  duration_minutes: number;
  status: string;
  type: {
    name: string;
    color: string;
    icon: string;
  };
  user_role: string;
  participants_count: number;
}

const props = defineProps<{
  instructor: any;
}>();

const upcomingSchedules = ref<Schedule[]>([]);
const loading = ref(true);

// Fetch upcoming schedules
const fetchUpcomingSchedules = async () => {
  try {
    loading.value = true;
    const response = await fetch(`/api/users/${props.instructor.user_id}/schedules/upcoming`);
    const data = await response.json();
    
    if (data.success) {
      upcomingSchedules.value = data.data.slice(0, 5); // Show next 5 events
    }
  } catch (error) {
    console.error('Failed to fetch schedules:', error);
  } finally {
    loading.value = false;
  }
};

// Format date for display
const formatDateTime = (datetime: string) => {
  const date = new Date(datetime);
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  });
};

// Group schedules by date
const schedulesByDate = computed(() => {
  const grouped: Record<string, Schedule[]> = {};
  
  upcomingSchedules.value.forEach(schedule => {
    const date = new Date(schedule.from_datetime).toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
    });
    
    if (!grouped[date]) {
      grouped[date] = [];
    }
    grouped[date].push(schedule);
  });
  
  return grouped;
});

onMounted(() => {
  fetchUpcomingSchedules();
});
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Existing instructor dashboard content -->
    
    <!-- Upcoming Schedules Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
          <Calendar class="w-5 h-5" />
          Upcoming Schedule
        </h2>
        <a href="/calendar" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
          View Full Calendar →
        </a>
      </div>

      <div v-if="loading" class="text-center py-8 text-gray-500">
        Loading schedules...
      </div>

      <div v-else-if="upcomingSchedules.length === 0" class="text-center py-8 text-gray-500">
        <Calendar class="w-12 h-12 mx-auto mb-2 opacity-50" />
        <p>No upcoming schedules</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="(schedules, date) in schedulesByDate" :key="date" class="space-y-2">
          <!-- Date Header -->
          <div class="text-sm font-medium text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-1">
            {{ date }}
          </div>

          <!-- Schedule Items -->
          <div
            v-for="schedule in schedules"
            :key="schedule.id"
            class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
          >
            <!-- Color Indicator -->
            <div 
              class="w-1 h-full rounded-full flex-shrink-0"
              :style="{ backgroundColor: schedule.type.color }"
            />

            <!-- Schedule Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <h3 class="font-medium text-gray-900 dark:text-white truncate">
                  {{ schedule.title }}
                </h3>
                <span 
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium flex-shrink-0"
                  :style="{ 
                    backgroundColor: schedule.type.color + '20',
                    color: schedule.type.color 
                  }"
                >
                  {{ schedule.type.name }}
                </span>
              </div>

              <div class="flex items-center gap-4 mt-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5" />
                  {{ formatDateTime(schedule.from_datetime) }}
                </span>
                <span v-if="schedule.location" class="flex items-center gap-1">
                  <MapPin class="w-3.5 h-3.5" />
                  {{ schedule.location }}
                </span>
                <span class="flex items-center gap-1">
                  <Users class="w-3.5 h-3.5" />
                  {{ schedule.participants_count }}
                </span>
              </div>

              <p v-if="schedule.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                {{ schedule.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
```

### Update StudentDashboard.vue

Similar implementation for `resources/js/dashboards/StudentDashboard.vue`:

```vue
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Calendar, Clock, Users, MapPin, AlertCircle } from 'lucide-vue-next';

interface Schedule {
  id: number;
  title: string;
  description: string;
  location: string;
  from_datetime: string;
  to_datetime: string;
  duration_minutes: number;
  status: string;
  type: {
    name: string;
    color: string;
    icon: string;
  };
  user_role: string;
  participation_status: string;
  participants_count: number;
}

const props = defineProps<{
  student: any;
}>();

const upcomingSchedules = ref<Schedule[]>([]);
const loading = ref(true);

// Fetch upcoming schedules
const fetchUpcomingSchedules = async () => {
  try {
    loading.value = true;
    const response = await fetch(`/api/users/${props.student.user_id}/schedules/upcoming`);
    const data = await response.json();
    
    if (data.success) {
      upcomingSchedules.value = data.data.slice(0, 5);
    }
  } catch (error) {
    console.error('Failed to fetch schedules:', error);
  } finally {
    loading.value = false;
  }
};

// Get today's schedules
const todaySchedules = computed(() => {
  const today = new Date().toDateString();
  return upcomingSchedules.value.filter(schedule => {
    return new Date(schedule.from_datetime).toDateString() === today;
  });
});

// Get pending invitations
const pendingInvitations = computed(() => {
  return upcomingSchedules.value.filter(
    schedule => schedule.participation_status === 'invited'
  );
});

const formatTime = (datetime: string) => {
  return new Date(datetime).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  });
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    invited: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    declined: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchUpcomingSchedules();
});
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Existing student dashboard content -->

    <!-- Today's Schedule -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
        <Calendar class="w-5 h-5" />
        Today's Schedule
      </h2>

      <div v-if="todaySchedules.length === 0" class="text-center py-4 text-gray-500">
        No classes scheduled for today
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="schedule in todaySchedules"
          :key="schedule.id"
          class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50"
        >
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <h3 class="font-medium text-gray-900 dark:text-white">
                {{ schedule.title }}
              </h3>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                :class="getStatusColor(schedule.participation_status)"
              >
                {{ schedule.participation_status }}
              </span>
            </div>
            <div class="flex items-center gap-3 mt-1 text-sm text-gray-600 dark:text-gray-400">
              <span>{{ formatTime(schedule.from_datetime) }} - {{ formatTime(schedule.to_datetime) }}</span>
              <span v-if="schedule.location">{{ schedule.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Invitations -->
    <div
      v-if="pendingInvitations.length > 0"
      class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4"
    >
      <div class="flex items-start gap-3">
        <AlertCircle class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
        <div class="flex-1">
          <h3 class="font-medium text-amber-900 dark:text-amber-200">
            Pending Invitations ({{ pendingInvitations.length }})
          </h3>
          <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
            You have schedule invitations that need your response
          </p>
          <button class="mt-2 text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">
            Review Invitations →
          </button>
        </div>
      </div>
    </div>

    <!-- Upcoming Schedules -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
          <Calendar class="w-5 h-5" />
          Upcoming This Week
        </h2>
        <a href="/calendar" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
          Full Calendar →
        </a>
      </div>

      <!-- Schedule list implementation (similar to instructor dashboard) -->
    </div>
  </div>
</template>
```

## Usage Examples

### Create an Activity Schedule
```typescript
const createActivitySchedule = async (activityId: number, participants: any[]) => {
  const response = await fetch('/api/schedules', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      schedule_type_id: 1, // activity
      title: 'Mathematics Quiz 1',
      description: 'Chapter 1-3 coverage',
      location: 'Room 101',
      from_datetime: '2025-10-15 09:00:00',
      to_datetime: '2025-10-15 10:00:00',
      schedulable_type: 'App\\Models\\Activity',
      schedulable_id: activityId,
      participants: participants,
    }),
  });
  return response.json();
};
```

### Drag-and-Drop Rescheduling
```typescript
const rescheduleEvent = async (scheduleId: number, newStart: Date, newEnd: Date) => {
  const response = await fetch(`/api/schedules/${scheduleId}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      from_datetime: newStart.toISOString(),
      to_datetime: newEnd.toISOString(),
    }),
  });
  return response.json();
};
```

### Accept Schedule Invitation
```typescript
const acceptInvitation = async (scheduleId: number, userId: number) => {
  const response = await fetch(`/api/schedules/${scheduleId}/participants/${userId}/status`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      participation_status: 'accepted',
    }),
  });
  return response.json();
};
```

## Next Steps

1. **Install FullCalendar** (Optional, for advanced calendar UI):
   ```bash
   npm install @fullcalendar/vue3 @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction
   ```

2. **Create Calendar Page** with drag-and-drop support

3. **Add Notifications** for upcoming schedules

4. **Implement Recurring Events** using the `recurrence_rule` field

5. **Add Room/Resource Management** with additional tables

6. **Sync with External Calendars** (Google Calendar, Outlook, etc.)

## Testing

Test the API endpoints:

```bash
# Create a schedule
curl -X POST http://localhost/api/schedules \
  -H "Content-Type: application/json" \
  -d '{
    "schedule_type_id": 1,
    "title": "Test Schedule",
    "from_datetime": "2025-10-15 09:00:00",
    "to_datetime": "2025-10-15 10:00:00",
    "participants": [{"user_id": 1, "role_in_schedule": "instructor"}]
  }'

# Get upcoming schedules
curl http://localhost/api/users/1/schedules/upcoming
```
