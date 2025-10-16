<script setup lang="ts">
import { ref, computed, onMounted, watch, withDefaults } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Calendar, Clock, MapPin, Users, AlertCircle, RefreshCw } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import ScheduleModal from '@/components/ScheduleModal.vue';
// @ts-ignore - Vue Cal doesn't have proper TypeScript definitions
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

interface Participant {
  id: number;
  name: string;
  role: string;
  status: string;
}

interface Schedule {
  id: number;
  title: string;
  description: string | null;
  location: string | null;
  from_datetime: string;
  to_datetime: string;
  status: string;
  is_recurring: boolean;
  recurrence_rule: string | null;
  type: {
    id: number;
    name: string;
    color: string;
    icon: string;
  };
  participants: Participant[];
  duration_minutes: number;
  created_by: number;
  schedulable_type: string | null;
  schedulable_id: number | null;
  deleted_at: string | null;
  course_details?: {
    session_number: number | null;
    topics_covered: string | null;
    required_materials: string | null;
    homework_assigned: string | null;
  } | null;
  activity_details?: {
    submission_deadline: string | null;
    points: number | null;
    instructions: string | null;
  } | null;
}

interface GroupedSchedules {
  [date: string]: Schedule[];
}

// Define props
interface Props {
  initialSchedules?: Schedule[];
}

const props = withDefaults(defineProps<Props>(), {
  initialSchedules: () => []
});

// Get authenticated user from Inertia page props
const page = usePage();
const user = computed(() => page.props.auth.user as any);

// State - Initialize with props
const schedules = ref<Schedule[]>(props.initialSchedules || []);
const loading = ref(false);
const error = ref<string | null>(null);
const viewMode = ref<'calendar' | 'list'>('calendar');
const selectedDate = ref<Date | string | null>(null);

// Modal state
const modalOpen = ref(false);
const selectedSchedule = ref<Schedule | null>(null);

// Detect dark mode
const isDarkMode = ref(document.documentElement.classList.contains('dark'));

// Create a reactive key for forcing calendar re-render (theme-based only)
const calendarKey = computed(() => `calendar-${isDarkMode.value ? 'dark' : 'light'}`);

// Watch for theme changes
if (typeof window !== 'undefined') {
  const observer = new MutationObserver(() => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
  });
  
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
}

// Fetch upcoming schedules (including soft-deleted)
const fetchSchedules = async () => {
  if (!user.value?.id) {
    error.value = 'User not authenticated';
    loading.value = false;
    return;
  }

  loading.value = true;
  error.value = null;
  
  // Use Inertia router to reload the page with fresh data
  // This ensures proper session handling
  router.reload({ only: ['initialSchedules'] });
};

// Handle event click
const handleEventClick = (event: any) => {
  const schedule = schedules.value.find(s => s.id === event.scheduleData.id);
  if (schedule) {
    selectedSchedule.value = schedule;
    modalOpen.value = true;
  }
};

// Handle schedule update
const handleScheduleUpdated = () => {
  fetchSchedules(); // Refresh the schedules list
};

// Handle schedule deletion
const handleScheduleDeleted = () => {
  fetchSchedules(); // Refresh the schedules list
};

// Format schedules for Vue Cal
const calendarEvents = computed(() => {
  console.log('🔍 Raw schedules:', schedules.value);
  
  const allEvents: any[] = [];
  
  schedules.value.forEach(schedule => {
    const startDate = new Date(schedule.from_datetime);
    const endDate = new Date(schedule.to_datetime);
    
    console.log('📅 Processing schedule:', {
      id: schedule.id,
      title: schedule.title,
      is_recurring: schedule.is_recurring,
      recurrence_rule: schedule.recurrence_rule,
      from_datetime: schedule.from_datetime,
      to_datetime: schedule.to_datetime,
    });
    
    // Normalize the schedule type name for CSS class
    const typeClass = schedule.type.name.toLowerCase().replace(/\s+/g, '-');
    
    // Use cancelled class for soft-deleted schedules
    const eventClass = schedule.deleted_at 
      ? 'schedule-cancelled' 
      : `schedule-${typeClass}`;
    
    if (schedule.is_recurring && schedule.recurrence_rule) {
      // For recurring schedules, generate multiple events
      const recurringEvents = generateRecurringEvents(schedule, startDate, endDate, eventClass);
      allEvents.push(...recurringEvents);
      console.log(`✅ Created ${recurringEvents.length} recurring events`);
    } else {
      // For non-recurring schedules, create single event
      const event = {
        start: startDate,
        end: endDate,
        title: schedule.deleted_at ? `[CANCELLED] ${schedule.title}` : schedule.title,
        content: schedule.description || '',
        class: eventClass,
        scheduleData: schedule,
      };
      allEvents.push(event);
      console.log('✅ Created single event:', event);
    }
  });
  
  console.log('📊 Total events for VueCal:', allEvents.length, allEvents);
  
  return allEvents;
});

// Generate recurring events based on recurrence rule
const generateRecurringEvents = (schedule: Schedule, rangeStart: Date, rangeEnd: Date, eventClass: string) => {
  const events: any[] = [];
  const rule = schedule.recurrence_rule?.toUpperCase() || '';
  
  // Extract time from the start datetime (event start time)
  const eventStartTime = {
    hours: rangeStart.getHours(),
    minutes: rangeStart.getMinutes(),
    seconds: rangeStart.getSeconds(),
  };
  
  // Extract time from the end datetime (event end time) 
  const eventEndTime = {
    hours: rangeEnd.getHours(),
    minutes: rangeEnd.getMinutes(),
    seconds: rangeEnd.getSeconds(),
  };
  
  // Get date range (start date to end date)
  const startDateOnly = new Date(rangeStart);
  startDateOnly.setHours(0, 0, 0, 0);
  
  const endDateOnly = new Date(rangeEnd);
  endDateOnly.setHours(0, 0, 0, 0);
  
  console.log('🔄 Generating recurring events:', {
    startDate: startDateOnly.toISOString(),
    endDate: endDateOnly.toISOString(),
    startTime: `${eventStartTime.hours}:${eventStartTime.minutes}`,
    endTime: `${eventEndTime.hours}:${eventEndTime.minutes}`,
    rule: rule,
  });
  
  // Parse recurrence rule
  let frequency: 'DAILY' | 'WEEKLY' | 'MONTHLY' = 'WEEKLY';
  if (rule.includes('FREQ=DAILY')) frequency = 'DAILY';
  else if (rule.includes('FREQ=WEEKLY')) frequency = 'WEEKLY';
  else if (rule.includes('FREQ=MONTHLY')) frequency = 'MONTHLY';
  
  // Parse BYDAY for weekly recurrence
  let byDays: number[] = []; // 0=Sunday, 1=Monday, ..., 6=Saturday
  if (frequency === 'WEEKLY' && rule.includes('BYDAY=')) {
    const byDayMatch = rule.match(/BYDAY=([A-Z,]+)/);
    if (byDayMatch) {
      const dayMap: Record<string, number> = {
        'SU': 0, 'MO': 1, 'TU': 2, 'WE': 3, 'TH': 4, 'FR': 5, 'SA': 6
      };
      const days = byDayMatch[1].split(',');
      byDays = days.map(day => dayMap[day]).filter(d => d !== undefined);
    }
  }
  
  // Generate events based on frequency
  let currentDate = new Date(startDateOnly);
  
  while (currentDate <= endDateOnly) {
    let shouldCreateEvent = true;
    
    // For weekly recurrence with BYDAY, only create events on specified days
    if (frequency === 'WEEKLY' && byDays.length > 0) {
      shouldCreateEvent = byDays.includes(currentDate.getDay());
    }
    
    if (shouldCreateEvent) {
      // Create event for this occurrence - SAME DAY, with start and end times
      const eventStart = new Date(currentDate);
      eventStart.setHours(eventStartTime.hours, eventStartTime.minutes, eventStartTime.seconds, 0);
      
      // End time is on the SAME DAY, just with different time
      const eventEnd = new Date(currentDate);
      eventEnd.setHours(eventEndTime.hours, eventEndTime.minutes, eventEndTime.seconds, 0);
      
      console.log('✅ Creating event:', {
        date: currentDate.toISOString().split('T')[0],
        start: eventStart.toISOString(),
        end: eventEnd.toISOString(),
      });
      
      events.push({
        start: eventStart,
        end: eventEnd,
        title: schedule.deleted_at ? `[CANCELLED] ${schedule.title}` : schedule.title,
        content: schedule.description || '',
        class: eventClass,
        scheduleData: schedule,
      });
    }
    
    // Move to next day (we'll filter by BYDAY if needed)
    currentDate.setDate(currentDate.getDate() + 1);
  }
  
  console.log(`✅ Generated ${events.length} recurring events`);
  
  return events;
};

// Group schedules by date for list view
const groupedSchedules = computed<GroupedSchedules>(() => {
  const grouped: GroupedSchedules = {};
  
  schedules.value.forEach(schedule => {
    const date = new Date(schedule.from_datetime).toDateString();
    if (!grouped[date]) {
      grouped[date] = [];
    }
    grouped[date].push(schedule);
  });
  
  return grouped;
});

// Format date for display
const formatDate = (dateString: string | number): string => {
  const date = new Date(dateString);
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  
  // Check if today
  if (date.toDateString() === today.toDateString()) {
    return 'Today';
  }
  
  // Check if tomorrow
  if (date.toDateString() === tomorrow.toDateString()) {
    return 'Tomorrow';
  }
  
  // Format as "Monday, Oct 14, 2025"
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

// Format time for display
const formatTime = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

// Get type badge color
const getTypeBadgeColor = (color: string): string => {
  // Map hex colors to Tailwind classes
  const colorMap: { [key: string]: string } = {
    '#3B82F6': 'bg-blue-100 text-blue-800 border-blue-200',
    '#10B981': 'bg-green-100 text-green-800 border-green-200',
    '#F59E0B': 'bg-amber-100 text-amber-800 border-amber-200',
    '#EF4444': 'bg-red-100 text-red-800 border-red-200',
    '#8B5CF6': 'bg-purple-100 text-purple-800 border-purple-200',
  };
  
  return colorMap[color] || 'bg-gray-100 text-gray-800 border-gray-200';
};

// Format duration
const formatDuration = (minutes: number): string => {
  if (minutes < 60) {
    return `${minutes} min`;
  }
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
};

// Get participant role badge color
const getRoleBadgeColor = (role: string | undefined): string => {
  if (!role) return 'bg-gray-50 text-gray-700 border-gray-200';
  
  const roleMap: { [key: string]: string } = {
    'instructor': 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'organizer': 'bg-purple-50 text-purple-700 border-purple-200',
    'student': 'bg-blue-50 text-blue-700 border-blue-200',
    'attendee': 'bg-gray-50 text-gray-700 border-gray-200',
    'proctor': 'bg-violet-50 text-violet-700 border-violet-200',
  };
  
  return roleMap[role.toLowerCase()] || 'bg-gray-50 text-gray-700 border-gray-200';
};

// Watch for prop changes
watch(() => props.initialSchedules, (newSchedules) => {
  if (newSchedules) {
    schedules.value = newSchedules;
    loading.value = false;
    
    // Auto-navigate to first event date
    if (newSchedules.length > 0) {
      selectedDate.value = new Date(newSchedules[0].from_datetime);
    }
  }
}, { immediate: true });

// Load schedules on mount (already loaded from props, but keep for refresh functionality)
onMounted(() => {
  // Data already loaded from Inertia props
  loading.value = false;
  
  // Set initial date to first event
  if (schedules.value.length > 0) {
    selectedDate.value = new Date(schedules.value[0].from_datetime);
  }
});
</script>

<template>
  <AppLayout>
    <Head title="My Schedule" />
    
    <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <Calendar class="w-8 h-8 text-indigo-600" />
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
              My Schedule
            </h1>
          </div>
          <p class="text-gray-600 dark:text-gray-400">
            View your upcoming classes, activities, and events
          </p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="flex flex-col items-center gap-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            <p class="text-gray-600 dark:text-gray-400">Loading your schedule...</p>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
          <div class="flex items-start gap-3">
            <AlertCircle class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
            <div>
              <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-1">
                Failed to Load Schedule
              </h3>
              <p class="text-red-700 dark:text-red-300">{{ error }}</p>
              <button 
                @click="fetchSchedules"
                class="mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
              >
                Try Again
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="schedules.length === 0" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12">
          <div class="text-center">
            <Calendar class="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
              No Upcoming Schedules
            </h3>
            <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
              You don't have any scheduled classes, activities, or events at the moment. 
              Check back later or contact your instructor if you think this is an error.
            </p>
          </div>
        </div>

        <!-- Schedule Display -->
        <div v-else class="space-y-4">
          <!-- View Toggle and Actions Bar -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-center">
              <!-- View Toggle -->
              <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button
                  @click="viewMode = 'calendar'"
                  :class="[
                    'flex items-center gap-2 px-4 py-2 rounded-md font-medium transition-colors',
                    viewMode === 'calendar'
                      ? 'bg-white dark:bg-gray-600 text-indigo-600 dark:text-indigo-400 shadow-sm'
                      : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
                  ]"
                >
                  <Calendar class="w-4 h-4" />
                  <span>Calendar</span>
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="[
                    'flex items-center gap-2 px-4 py-2 rounded-md font-medium transition-colors',
                    viewMode === 'list'
                      ? 'bg-white dark:bg-gray-600 text-indigo-600 dark:text-indigo-400 shadow-sm'
                      : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                  <span>List</span>
                </button>
              </div>

              <!-- Refresh Button -->
              <button 
                @click="fetchSchedules"
                :disabled="loading"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors shadow-sm hover:shadow-md"
              >
                <RefreshCw :class="{ 'animate-spin': loading }" class="w-4 h-4" />
                <span v-if="loading">Refreshing...</span>
                <span v-else>Refresh</span>
              </button>
            </div>
          </div>

          <!-- Calendar View -->
          <div v-if="viewMode === 'calendar'" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <!-- Debug Info -->
            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
              <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>{{ schedules.length }}</strong> schedule(s) loaded | 
                <strong>{{ calendarEvents.length }}</strong> event(s) formatted for calendar
              </p>
              <p v-if="schedules.length > 0" class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                First event: {{ new Date(schedules[0].from_datetime).toLocaleString() }}
                <span v-if="selectedDate"> | Showing: {{ new Date(selectedDate).toLocaleString() }}</span>
              </p>
              <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                📊 Check browser console for detailed event data
              </p>
            </div>
            
            <VueCal
              :key="calendarKey"
              :events="calendarEvents"
              :selected-date="selectedDate"
              :time-from="0 * 60"
              :time-to="24 * 60"
              :disable-views="['years']"
              default-view="week"
              :editable-events="false"
              events-on-month-view="short"
              :snap-to-time="15"
              hide-view-selector
              twelve-hour
              :class="['vuecal--rounded', isDarkMode ? 'vuecal--dark-mode' : 'vuecal--light-mode']"
              style="height: 650px"
              @event-click="handleEventClick"
            >
            <!-- Custom event content slot -->
            <template #event="{ event }">
              <div class="custom-event-content">
                <div class="event-time">
                  {{ new Date(event.start).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) }}
                </div>
                <div class="event-title">
                  {{ event.title }}
                </div>
                <div v-if="event.scheduleData?.location" class="event-location">
                  <MapPin class="w-3 h-3 inline" />
                  {{ event.scheduleData.location }}
                </div>
              </div>
            </template>
          </VueCal>

            <!-- Legend -->
            <div class="mt-6 flex flex-wrap gap-4 justify-center">
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #3B82F6"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Activity</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #10B981"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Course</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #14B8A6"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Course Due Date</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #F59E0B"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Adhoc</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #EF4444"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Exam</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #8B5CF6"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Office Hours</span>
              </div>
            </div>
          </div>

          <!-- List View -->
          <div v-if="viewMode === 'list'" class="space-y-8">
            <!-- Group by Date -->
            <div 
              v-for="(daySchedules, date) in groupedSchedules" 
              :key="date"
              class="space-y-4"
            >
              <!-- Date Header (Sticky) -->
              <div class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900 py-3 px-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                  {{ formatDate(date) }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ daySchedules.length }} {{ daySchedules.length === 1 ? 'event' : 'events' }}
                </p>
              </div>

              <!-- Schedule Cards for this Date -->
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="schedule in daySchedules"
                  :key="schedule.id"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200 overflow-hidden group"
                >
                  <!-- Card Content -->
                  <div class="p-5 space-y-4">
                    <!-- Type Badge -->
                    <div class="flex items-start justify-between gap-2">
                      <span 
                        :class="getTypeBadgeColor(schedule.type.color)"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border"
                      >
                        {{ schedule.type.name }}
                      </span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDuration(schedule.duration_minutes) }}
                      </span>
                    </div>

                    <!-- Title -->
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ schedule.title }}
                      </h3>
                      <p v-if="schedule.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                        {{ schedule.description }}
                      </p>
                    </div>

                    <!-- Time -->
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                      <Clock class="w-4 h-4 text-gray-400" />
                      <span class="font-medium">
                        {{ formatTime(schedule.from_datetime) }} - {{ formatTime(schedule.to_datetime) }}
                      </span>
                    </div>

                    <!-- Location -->
                    <div v-if="schedule.location" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                      <MapPin class="w-4 h-4 text-gray-400" />
                      <span>{{ schedule.location }}</span>
                    </div>

                    <!-- Participants -->
                    <div v-if="schedule.participants.length > 0" class="space-y-2">
                      <div class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <Users class="w-4 h-4 text-gray-400" />
                        <span>Participants ({{ schedule.participants.length }})</span>
                      </div>
                      
                      <!-- Participants List -->
                      <div class="space-y-1.5 max-h-32 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                        <div
                          v-for="participant in schedule.participants"
                          :key="participant.id"
                          class="flex items-center justify-between gap-2 text-sm bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2"
                        >
                          <span class="text-gray-900 dark:text-gray-100 font-medium truncate">
                            {{ participant.name }}
                          </span>
                          <span 
                            :class="getRoleBadgeColor(participant.role)"
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border flex-shrink-0"
                          >
                            {{ participant.role }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                      <span 
                        :class="{
                          'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400': schedule.status === 'scheduled',
                          'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400': schedule.status === 'in_progress',
                          'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-400': schedule.status === 'completed',
                          'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400': schedule.status === 'cancelled'
                        }"
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border"
                      >
                        {{ schedule.status.replace('_', ' ').toUpperCase() }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Modal -->
    <ScheduleModal 
      v-if="selectedSchedule"
      :schedule="selectedSchedule"
      :open="modalOpen"
      :currentUserId="user.id"
      :userRole="user.role"
      @update:open="modalOpen = $event"
      @updated="handleScheduleUpdated"
      @deleted="handleScheduleDeleted"
    />
  </AppLayout>
</template>

<style scoped>
/* Vue Cal Custom Styles */
.vuecal--rounded {
  border-radius: 0.5rem;
  overflow: hidden;
}

/* Event styling */
:deep(.vuecal__event) {
  border-radius: 0.5rem;
  border: none !important;
  padding: 0.5rem !important;
  overflow: visible !important;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  transition: all 0.2s ease;
  cursor: pointer;
}

:deep(.vuecal__event:hover) {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Custom event content styling */
:deep(.custom-event-content) {
  color: white !important;
  font-size: 0.75rem;
  line-height: 1.3;
  height: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

:deep(.custom-event-content .event-time) {
  font-weight: 700;
  font-size: 0.6875rem;
  opacity: 0.95;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

:deep(.custom-event-content .event-title) {
  font-weight: 600;
  font-size: 0.8125rem;
  line-height: 1.2;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  flex: 1;
}

:deep(.custom-event-content .event-location) {
  font-size: 0.6875rem;
  opacity: 0.9;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Calendar container - force theme colors with class-based specificity */
:deep(.vuecal--light-mode) {
  background-color: white !important;
}

:deep(.vuecal--dark-mode) {
  background-color: #1f2937 !important;
}

:deep(.vuecal) {
  background-color: white;
}

:deep(.dark .vuecal) {
  background-color: #1f2937 !important;
}

/* Calendar header styling */
:deep(.vuecal--light-mode .vuecal__title-bar) {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
}

:deep(.vuecal--dark-mode .vuecal__title-bar) {
  background-color: #111827 !important;
  border-bottom-color: #374151 !important;
}

:deep(.vuecal--light-mode .vuecal__title) {
  color: #111827 !important;
  font-weight: 600;
}

:deep(.vuecal--dark-mode .vuecal__title) {
  color: #f3f4f6 !important;
  font-weight: 600;
}

/* Fallback for browsers that don't support the class binding */
:deep(.vuecal__title-bar) {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
}

:deep(.dark .vuecal__title-bar) {
  background-color: #111827 !important;
  border-bottom-color: #374151 !important;
}

:deep(.vuecal__title) {
  color: #111827 !important;
  font-weight: 600;
}

:deep(.dark .vuecal__title) {
  color: #f3f4f6 !important;
}

/* Weekday headers */
:deep(.vuecal--light-mode .vuecal__weekdays-headings) {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
}

:deep(.vuecal--dark-mode .vuecal__weekdays-headings) {
  background-color: #111827 !important;
  border-bottom-color: #374151 !important;
}

:deep(.vuecal--light-mode .vuecal__heading) {
  color: #374151 !important;
  font-weight: 600;
}

:deep(.vuecal--dark-mode .vuecal__heading) {
  color: #d1d5db !important;
  font-weight: 600;
}

/* Fallback */
:deep(.vuecal__weekdays-headings) {
  background-color: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
}

:deep(.dark .vuecal__weekdays-headings) {
  background-color: #111827 !important;
  border-bottom-color: #374151 !important;
}

:deep(.vuecal__heading) {
  color: #374151 !important;
  font-weight: 600;
}

:deep(.dark .vuecal__heading) {
  color: #d1d5db !important;
}

/* Cell styling */
:deep(.vuecal--light-mode .vuecal__cell) {
  background-color: white !important;
  border-color: #e5e7eb !important;
}

:deep(.vuecal--dark-mode .vuecal__cell) {
  background-color: #1f2937 !important;
  border-color: #374151 !important;
}

:deep(.vuecal--light-mode .vuecal__cell--today) {
  background-color: #dbeafe !important;
}

:deep(.vuecal--dark-mode .vuecal__cell--today) {
  background-color: #1e3a8a !important;
}

:deep(.vuecal--light-mode .vuecal__cell--selected) {
  background-color: #f0f9ff !important;
}

:deep(.vuecal--dark-mode .vuecal__cell--selected) {
  background-color: #172554 !important;
}

/* Fallback */
:deep(.vuecal__cell) {
  background-color: white !important;
  border-color: #e5e7eb !important;
}

:deep(.dark .vuecal__cell) {
  background-color: #1f2937 !important;
  border-color: #374151 !important;
}

:deep(.vuecal__cell--today) {
  background-color: #dbeafe !important;
}

:deep(.dark .vuecal__cell--today) {
  background-color: #1e3a8a !important;
}

:deep(.vuecal__cell--selected) {
  background-color: #f0f9ff !important;
}

:deep(.dark .vuecal__cell--selected) {
  background-color: #172554 !important;
}

/* Time column */
:deep(.vuecal--light-mode .vuecal__time-column) {
  background-color: #f9fafb !important;
  border-right: 1px solid #e5e7eb !important;
}

:deep(.vuecal--dark-mode .vuecal__time-column) {
  background-color: #111827 !important;
  border-right-color: #374151 !important;
}

:deep(.vuecal--light-mode .vuecal__time-cell-line) {
  color: #6b7280 !important;
}

:deep(.vuecal--dark-mode .vuecal__time-cell-line) {
  color: #9ca3af !important;
}

/* Fallback */
:deep(.vuecal__time-column) {
  background-color: #f9fafb !important;
  border-right: 1px solid #e5e7eb !important;
}

:deep(.dark .vuecal__time-column) {
  background-color: #111827 !important;
  border-right-color: #374151 !important;
}

:deep(.vuecal__time-cell-line) {
  color: #6b7280 !important;
}

:deep(.dark .vuecal__time-cell-line) {
  color: #9ca3af !important;
}

:deep(.dark .vuecal__time-cell-line) {
  color: #9ca3af;
}

/* Navigation buttons */
:deep(.vuecal__arrow) {
  color: #4f46e5;
}

:deep(.dark .vuecal__arrow) {
  color: #818cf8;
}

:deep(.vuecal__arrow:hover) {
  background-color: #eef2ff;
}

:deep(.dark .vuecal__arrow:hover) {
  background-color: #312e81;
}

/* View buttons */
:deep(.vuecal__view-btn) {
  color: #374151;
  border-color: #d1d5db;
}

:deep(.dark .vuecal__view-btn) {
  color: #d1d5db;
  border-color: #4b5563;
}

:deep(.vuecal__view-btn--active) {
  background-color: #4f46e5;
  color: white;
}

:deep(.dark .vuecal__view-btn--active) {
  background-color: #6366f1;
}

/* No event styling */
:deep(.vuecal__no-event) {
  color: #9ca3af;
  font-size: 0.875rem;
  font-weight: 500;
}

:deep(.dark .vuecal__no-event) {
  color: #6b7280;
}

/* Current time indicator */
:deep(.vuecal__now-line) {
  color: #ef4444;
  border-top: 2px solid #ef4444;
}

:deep(.dark .vuecal__now-line) {
  color: #f87171;
  border-top-color: #f87171;
}

/* Scrollbar for calendar */
:deep(.vuecal__bg)::-webkit-scrollbar {
  width: 8px;
}

:deep(.vuecal__bg)::-webkit-scrollbar-track {
  background: #f3f4f6;
}

:deep(.dark .vuecal__bg)::-webkit-scrollbar-track {
  background: #1f2937;
}

:deep(.vuecal__bg)::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

:deep(.dark .vuecal__bg)::-webkit-scrollbar-thumb {
  background: #4b5563;
}

:deep(.vuecal__bg)::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

:deep(.dark .vuecal__bg)::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Animate spin for refresh button */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Line clamp utility for list view */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Scrollbar for participants list */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.dark .scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
  background: #4b5563;
}

/* Schedule type event classes - matches the colors from the legend */

/* Fallback for all schedule events (in case type doesn't match predefined classes) */
:deep(.vuecal__event[class*="schedule-"]) {
  background-color: #6366f1 !important; /* Indigo fallback */
  border-color: #4f46e5 !important;
  color: white !important;
}

:deep(.vuecal__event.schedule-activity) {
  background-color: #3B82F6 !important;
  border-color: #2563EB !important;
  color: white !important;
}

:deep(.vuecal__event.schedule-course) {
  background-color: #10B981 !important;
  border-color: #059669 !important;
  color: white !important;
}

/* Course due date - lighter green variation */
:deep(.vuecal__event.schedule-course-due-date) {
  background-color: #14B8A6 !important; /* Teal */
  border-color: #0D9488 !important;
  color: white !important;
}

:deep(.vuecal__event.schedule-adhoc) {
  background-color: #F59E0B !important;
  border-color: #D97706 !important;
  color: white !important;
}

:deep(.vuecal__event.schedule-exam) {
  background-color: #EF4444 !important;
  border-color: #DC2626 !important;
  color: white !important;
}

:deep(.vuecal__event.schedule-office-hours) {
  background-color: #8B5CF6 !important;
  border-color: #7C3AED !important;
  color: white !important;
}

/* Ensure event content text is always white */
:deep(.vuecal__event.schedule-activity .custom-event-content),
:deep(.vuecal__event.schedule-course .custom-event-content),
:deep(.vuecal__event.schedule-course-due-date .custom-event-content),
:deep(.vuecal__event.schedule-adhoc .custom-event-content),
:deep(.vuecal__event.schedule-exam .custom-event-content),
:deep(.vuecal__event.schedule-office-hours .custom-event-content) {
  color: white !important;
}

:deep(.vuecal__event.schedule-activity .custom-event-content *),
:deep(.vuecal__event.schedule-course .custom-event-content *),
:deep(.vuecal__event.schedule-course-due-date .custom-event-content *),
:deep(.vuecal__event.schedule-adhoc .custom-event-content *),
:deep(.vuecal__event.schedule-exam .custom-event-content *),
:deep(.vuecal__event.schedule-office-hours .custom-event-content *) {
  color: white !important;
}

/* Cancelled schedules styling - red with strikethrough effect */
:deep(.vuecal__event.schedule-cancelled) {
  background-color: #DC2626 !important;
  border-color: #B91C1C !important;
  color: white !important;
  opacity: 0.85;
}

:deep(.vuecal__event.schedule-cancelled .custom-event-content) {
  color: white !important;
  opacity: 0.95;
}

:deep(.vuecal__event.schedule-cancelled .custom-event-content *) {
  color: white !important;
}

:deep(.vuecal__event.schedule-cancelled .event-title) {
  text-decoration: line-through;
  font-style: italic;
}

/* Hover effect for cancelled events */
:deep(.vuecal__event.schedule-cancelled:hover) {
  opacity: 1;
  background-color: #EF4444 !important;
}
</style>
