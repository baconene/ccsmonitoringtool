<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div 
        :class="className"
        v-bind="$attrs"
        class="flex items-center justify-center"
    >
        <img 
            src="/images/team-lema-logo.svg" 
            alt="Team LEMA" 
            class="w-full h-full object-contain"
        />
    </div>
</template>
