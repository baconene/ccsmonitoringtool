<script setup lang="ts">
import { Mail, Phone, MapPin, Clock } from 'lucide-vue-next';

interface Props {
  email: string;
  phone: string | null;
  officeLocation: string | null;
  officeHours: string | null;
}

defineProps<Props>();
</script>

<template>
  <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-6 border border-purple-200 dark:border-purple-800">
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Contact Information</h2>
    
    <div class="space-y-4">
      <!-- Email -->
      <div class="flex items-start gap-3">
        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex-shrink-0">
          <Mail class="w-4 h-4 text-purple-600 dark:text-purple-400" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</p>
          <a
            :href="`mailto:${email}`"
            class="text-sm text-purple-600 dark:text-purple-400 hover:underline break-all"
          >
            {{ email }}
          </a>
        </div>
      </div>

      <!-- Phone -->
      <div v-if="phone" class="flex items-start gap-3">
        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex-shrink-0">
          <Phone class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Phone</p>
          <a
            :href="`tel:${phone}`"
            class="text-sm text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400"
          >
            {{ phone }}
          </a>
        </div>
      </div>

      <!-- Office Location -->
      <div v-if="officeLocation" class="flex items-start gap-3">
        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg flex-shrink-0">
          <MapPin class="w-4 h-4 text-green-600 dark:text-green-400" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Office</p>
          <p class="text-sm text-gray-900 dark:text-gray-100">{{ officeLocation }}</p>
        </div>
      </div>

      <!-- Office Hours -->
      <div v-if="officeHours" class="flex items-start gap-3">
        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex-shrink-0">
          <Clock class="w-4 h-4 text-orange-600 dark:text-orange-400" />
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Office Hours</p>
          <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ officeHours }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
