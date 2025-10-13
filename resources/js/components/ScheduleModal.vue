<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { X, Calendar, Clock, MapPin, Users, Trash2, Save, ExternalLink, AlertCircle } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Participant {
  id: number;
  name: string;
  role: string;
  status: string;
}

interface ScheduleType {
  id: number;
  name: string;
  color: string;
  icon: string;
}

interface Schedule {
  id: number;
  title: string;
  description: string | null;
  location: string | null;
  from_datetime: string;
  to_datetime: string;
  status: string;
  type: ScheduleType;
  participants: Participant[];
  duration_minutes: number;
  created_by: number;
  schedulable_type: string | null;
  schedulable_id: number | null;
  deleted_at: string | null;
}

interface Props {
  schedule: Schedule | null;
  open: boolean;
  currentUserId: number;
  userRole: string;
}

interface Emits {
  (e: 'update:open', value: boolean): void;
  (e: 'updated'): void;
  (e: 'deleted'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// Form state
const form = ref({
  title: '',
  description: '',
  location: '',
  from_datetime: '',
  to_datetime: '',
  status: 'scheduled',
});

const saving = ref(false);
const deleting = ref(false);
const error = ref<string | null>(null);

// Check if current user is the creator
const isCreator = computed(() => {
  return props.schedule?.created_by === props.currentUserId;
});

// Check if schedule is cancelled (soft deleted)
const isCancelled = computed(() => {
  return props.schedule?.deleted_at !== null;
});

// Format datetime for input field
const formatDateTimeForInput = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toISOString().slice(0, 16); // Format: YYYY-MM-DDTHH:mm
};

// Watch for schedule changes and populate form
watch(() => props.schedule, (newSchedule) => {
  if (newSchedule) {
    form.value = {
      title: newSchedule.title,
      description: newSchedule.description || '',
      location: newSchedule.location || '',
      from_datetime: formatDateTimeForInput(newSchedule.from_datetime),
      to_datetime: formatDateTimeForInput(newSchedule.to_datetime),
      status: newSchedule.status,
    };
  }
}, { immediate: true });

// Close modal
const closeModal = () => {
  emit('update:open', false);
  error.value = null;
};

// Save schedule changes
const saveSchedule = async () => {
  if (!props.schedule || !isCreator.value) return;

  try {
    saving.value = true;
    error.value = null;

    const response = await axios.put(`/api/schedules/${props.schedule.id}`, {
      title: form.value.title,
      description: form.value.description,
      location: form.value.location,
      from_datetime: form.value.from_datetime,
      to_datetime: form.value.to_datetime,
      status: form.value.status,
    });

    if (response.data.success) {
      emit('updated');
      closeModal();
    }
  } catch (err: any) {
    console.error('Error saving schedule:', err);
    error.value = err.response?.data?.message || 'Failed to save schedule';
  } finally {
    saving.value = false;
  }
};

// Delete schedule
const deleteSchedule = async () => {
  if (!props.schedule || !isCreator.value) return;
  
  if (!confirm('Are you sure you want to cancel this schedule?')) return;

  try {
    deleting.value = true;
    error.value = null;

    const response = await axios.delete(`/api/schedules/${props.schedule.id}`);

    if (response.data.success) {
      emit('deleted');
      closeModal();
    }
  } catch (err: any) {
    console.error('Error deleting schedule:', err);
    error.value = err.response?.data?.message || 'Failed to delete schedule';
  } finally {
    deleting.value = false;
  }
};

// Navigate to related item
const navigateToRelatedItem = () => {
  if (!props.schedule?.schedulable_type || !props.schedule?.schedulable_id) return;

  const type = props.schedule.schedulable_type.split('\\').pop()?.toLowerCase();
  
  if (type === 'activity') {
    if (props.userRole === 'instructor' || props.userRole === 'admin') {
      router.visit(`/activities/${props.schedule.schedulable_id}`);
    } else if (props.userRole === 'student') {
      // For students, we need to find the course/module context
      // This would require additional data - for now, just show a message
      alert('Please access this activity through your course modules');
    }
  } else if (type === 'course') {
    router.visit(`/courses/${props.schedule.schedulable_id}`);
  }
};

// Get schedulable type display name
const getSchedulableTypeName = computed(() => {
  if (!props.schedule?.schedulable_type) return null;
  return props.schedule.schedulable_type.split('\\').pop();
});

// Format date for display
const formatDateTime = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  });
};

// Get role badge color
const getRoleBadgeColor = (role: string): string => {
  const roleMap: Record<string, string> = {
    organizer: 'bg-purple-100 text-purple-800 border-purple-200',
    instructor: 'bg-blue-100 text-blue-800 border-blue-200',
    student: 'bg-green-100 text-green-800 border-green-200',
    participant: 'bg-gray-100 text-gray-800 border-gray-200',
  };
  return roleMap[role.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-200';
};
</script>

<template>
  <Dialog :open="open" @update:open="closeModal">
    <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle class="flex items-center justify-between">
          <span class="flex items-center gap-2">
            <Calendar class="w-5 h-5" />
            {{ isCancelled ? 'Cancelled Schedule' : 'Schedule Details' }}
          </span>
          <button @click="closeModal" class="hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full p-1">
            <X class="w-5 h-5" />
          </button>
        </DialogTitle>
      </DialogHeader>

      <div v-if="schedule" class="space-y-6">
        <!-- Cancelled Badge -->
        <div v-if="isCancelled" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <AlertCircle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
            <div>
              <h4 class="font-semibold text-red-900 dark:text-red-100">This schedule has been cancelled</h4>
              <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                Cancelled on {{ formatDateTime(schedule.deleted_at!) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
          <p class="text-red-800 dark:text-red-200">{{ error }}</p>
        </div>

        <!-- Read-only view for non-creators or cancelled schedules -->
        <div v-if="!isCreator || isCancelled" class="space-y-4">
          <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ schedule.title }}</h3>
            <p v-if="schedule.description" class="text-gray-600 dark:text-gray-400 mt-2">{{ schedule.description }}</p>
          </div>

          <div class="grid gap-4">
            <div class="flex items-start gap-3">
              <Clock class="w-5 h-5 text-gray-500 mt-0.5" />
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Start</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatDateTime(schedule.from_datetime) }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <Clock class="w-5 h-5 text-gray-500 mt-0.5" />
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">End</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatDateTime(schedule.to_datetime) }}</p>
              </div>
            </div>

            <div v-if="schedule.location" class="flex items-start gap-3">
              <MapPin class="w-5 h-5 text-gray-500 mt-0.5" />
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Location</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ schedule.location }}</p>
              </div>
            </div>
          </div>

          <!-- Participants -->
          <div v-if="schedule.participants.length > 0">
            <h4 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-gray-100 mb-3">
              <Users class="w-4 h-4" />
              Participants ({{ schedule.participants.length }})
            </h4>
            <div class="space-y-2">
              <div
                v-for="participant in schedule.participants"
                :key="participant.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
              >
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ participant.name }}</span>
                <span :class="['text-xs px-2 py-1 rounded-full border', getRoleBadgeColor(participant.role)]">
                  {{ participant.role }}
                </span>
              </div>
            </div>
          </div>

          <!-- Related Item -->
          <div v-if="getSchedulableTypeName" class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              @click="navigateToRelatedItem"
              class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium"
            >
              <ExternalLink class="w-4 h-4" />
              View {{ getSchedulableTypeName }}
            </button>
          </div>
        </div>

        <!-- Editable form for creators -->
        <form v-else @submit.prevent="saveSchedule" class="space-y-4">
          <div>
            <Label for="title">Title</Label>
            <Input id="title" v-model="form.title" required />
          </div>

          <div>
            <Label for="description">Description</Label>
            <Textarea id="description" v-model="form.description" rows="3" />
          </div>

          <div>
            <Label for="location">Location</Label>
            <Input id="location" v-model="form.location" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="from_datetime">Start Date & Time</Label>
              <Input id="from_datetime" type="datetime-local" v-model="form.from_datetime" required />
            </div>

            <div>
              <Label for="to_datetime">End Date & Time</Label>
              <Input id="to_datetime" type="datetime-local" v-model="form.to_datetime" required />
            </div>
          </div>

          <!-- Participants (read-only in edit mode) -->
          <div v-if="schedule.participants.length > 0">
            <Label>Participants ({{ schedule.participants.length }})</Label>
            <div class="mt-2 space-y-2">
              <div
                v-for="participant in schedule.participants"
                :key="participant.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
              >
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ participant.name }}</span>
                <span :class="['text-xs px-2 py-1 rounded-full border', getRoleBadgeColor(participant.role)]">
                  {{ participant.role }}
                </span>
              </div>
            </div>
          </div>

          <!-- Related Item -->
          <div v-if="getSchedulableTypeName" class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
              Linked to: <strong>{{ getSchedulableTypeName }} #{{ schedule.schedulable_id }}</strong>
            </p>
            <button
              type="button"
              @click="navigateToRelatedItem"
              class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm"
            >
              <ExternalLink class="w-4 h-4" />
              View {{ getSchedulableTypeName }}
            </button>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
              ⚠️ Updating this schedule will also update the {{ getSchedulableTypeName }}'s due date
            </p>
          </div>

          <DialogFooter class="flex justify-between gap-2">
            <Button
              type="button"
              variant="destructive"
              @click="deleteSchedule"
              :disabled="deleting || saving"
              class="flex items-center gap-2"
            >
              <Trash2 class="w-4 h-4" />
              {{ deleting ? 'Cancelling...' : 'Cancel Schedule' }}
            </Button>
            <div class="flex gap-2">
              <Button type="button" variant="outline" @click="closeModal" :disabled="saving || deleting">
                Close
              </Button>
              <Button type="submit" :disabled="saving || deleting" class="flex items-center gap-2">
                <Save class="w-4 h-4" />
                {{ saving ? 'Saving...' : 'Save Changes' }}
              </Button>
            </div>
          </DialogFooter>
        </form>
      </div>
    </DialogContent>
  </Dialog>
</template>
