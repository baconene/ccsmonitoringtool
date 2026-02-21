<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { CheckCircle, AlertCircle, Info, AlertTriangle, X } from 'lucide-vue-next';

const { toasts, remove } = useToast();

const getIcon = (type: string) => {
  switch (type) {
    case 'success':
      return CheckCircle;
    case 'error':
      return AlertCircle;
    case 'warning':
      return AlertTriangle;
    case 'info':
    default:
      return Info;
  }
};

const getColor = (type: string) => {
  switch (type) {
    case 'success':
      return {
        bg: 'bg-green-50 dark:bg-green-950',
        border: 'border-green-200 dark:border-green-800',
        text: 'text-green-900 dark:text-green-100',
        icon: 'text-green-600 dark:text-green-400'
      };
    case 'error':
      return {
        bg: 'bg-red-50 dark:bg-red-950',
        border: 'border-red-200 dark:border-red-800',
        text: 'text-red-900 dark:text-red-100',
        icon: 'text-red-600 dark:text-red-400'
      };
    case 'warning':
      return {
        bg: 'bg-yellow-50 dark:bg-yellow-950',
        border: 'border-yellow-200 dark:border-yellow-800',
        text: 'text-yellow-900 dark:text-yellow-100',
        icon: 'text-yellow-600 dark:text-yellow-400'
      };
    case 'info':
    default:
      return {
        bg: 'bg-blue-50 dark:bg-blue-950',
        border: 'border-blue-200 dark:border-blue-800',
        text: 'text-blue-900 dark:text-blue-100',
        icon: 'text-blue-600 dark:text-blue-400'
      };
  }
};
</script>

<template>
  <div class="fixed inset-0 pointer-events-none z-50">
    <!-- Mobile: top -->
    <div
      class="flex flex-col gap-3 p-4 md:hidden"
      :class="toasts.length > 0 ? 'top-0' : 'pointer-events-none'"
    >
      <transition-group name="toast-slide-down">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'flex items-start gap-3 px-4 py-3 rounded-lg border backdrop-blur-sm pointer-events-auto',
            getColor(toast.type).bg,
            getColor(toast.type).border,
            getColor(toast.type).text,
            'animate-in slide-in-from-top-2 fade-in'
          ]"
        >
          <component
            :is="getIcon(toast.type)"
            class="w-5 h-5 flex-shrink-0 mt-0.5"
            :class="getColor(toast.type).icon"
          />
          <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
          <button
            @click="remove(toast.id)"
            class="flex-shrink-0 text-current hover:opacity-70 transition-opacity"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </transition-group>
    </div>

    <!-- Desktop: bottom right -->
    <div
      class="hidden md:flex flex-col gap-3 p-4 items-end bottom-0 right-0"
      :class="toasts.length > 0 ? 'absolute' : 'pointer-events-none'"
    >
      <transition-group name="toast-slide-up">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'flex items-start gap-3 px-4 py-3 rounded-lg border backdrop-blur-sm pointer-events-auto max-w-sm',
            getColor(toast.type).bg,
            getColor(toast.type).border,
            getColor(toast.type).text,
            'animate-in slide-in-from-bottom-2 fade-in'
          ]"
        >
          <component
            :is="getIcon(toast.type)"
            class="w-5 h-5 flex-shrink-0 mt-0.5"
            :class="getColor(toast.type).icon"
          />
          <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
          <button
            @click="remove(toast.id)"
            class="flex-shrink-0 text-current hover:opacity-70 transition-opacity"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<style scoped>
  .toast-slide-down-enter-active,
  .toast-slide-down-leave-active,
  .toast-slide-up-enter-active,
  .toast-slide-up-leave-active {
    transition: all 0.3s ease;
  }

  .toast-slide-down-enter-from {
    transform: translateY(-20px);
    opacity: 0;
  }

  .toast-slide-down-leave-to {
    transform: translateY(-20px);
    opacity: 0;
  }

  .toast-slide-up-enter-from {
    transform: translateY(20px);
    opacity: 0;
  }

  .toast-slide-up-leave-to {
    transform: translateY(20px);
    opacity: 0;
  }
</style>
