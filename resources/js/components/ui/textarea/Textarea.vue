<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import { cn } from '@/lib/utils';

const props = withDefaults(defineProps<{
  modelValue?: string | number;
  class?: string;
  placeholder?: string;
  rows?: number;
  disabled?: boolean;
}>(), {
  modelValue: '',
  rows: 4,
  disabled: false,
});

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits);
</script>

<template>
  <textarea
    v-model="modelValue"
    :class="cn(
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
      props.class
    )"
    :placeholder="placeholder"
    :rows="rows"
    :disabled="disabled"
  />
</template>
