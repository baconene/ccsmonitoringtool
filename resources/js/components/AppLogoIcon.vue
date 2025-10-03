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
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 48 48"
        :class="className"
        v-bind="$attrs"
        :style="{ color: 'currentColor' }"
        >
        <!-- Graduation cap base -->
        <polygon
            :fill="(($attrs.style as Record<string, any>)?.color) ? 'currentColor' : 'var(--app-logo-color, currentColor)'"
            points="24,8 46,18 24,28 2,18"
        />
        <!-- Cap top shadow -->
        <polygon
            fill-opacity="0.15"
            :fill="($attrs.style && ($attrs.style as Record<string, any>).color) ? 'currentColor' : 'var(--app-logo-color, currentColor)'"
            points="24,8 46,18 24,28"
        />
        <!-- Cap band -->
        <rect
            x="14"
            y="28"
            width="20"
            height="6"
            rx="2"
            :fill="($attrs.style && ($attrs.style as Record<string, any>).color) ? 'currentColor' : 'var(--app-logo-color, currentColor)'"
        />
        <!-- Tassel string -->
        <line
            x1="24"
            y1="28"
            x2="24"
            y2="38"
            :stroke="(($attrs.style as Record<string, any>)?.color) ? 'currentColor' : 'var(--app-logo-color, currentColor)'"
            stroke-width="2"
            stroke-linecap="round"
        />
        <!-- Tassel knot -->
        <circle
            cx="24"
            cy="38"
            r="2"
            :fill="($attrs.style && ($attrs.style as Record<string, any>).color) ? 'currentColor' : 'var(--app-logo-color, currentColor)'"
        />
        </svg>
</template>
