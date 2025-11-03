<script setup lang="ts">
// No imports needed for the Team LEMA logo
</script>

<template>
    <div class="flex items-center gap-3">
        <!-- Team LEMA Logo with theme-adaptive background -->
        <div class="h-10 w-10 rounded-lg bg-white dark:bg-gray-800 p-1.5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <img 
                src="/images/team-lema-logo.svg" 
                alt="Team LEMA Logo" 
                class="h-full w-full object-contain"
            />
        </div>
        <div class="flex flex-col">
            <!-- Team LEMA Text -->
            <div class="text-xl font-bold text-gray-900 dark:text-white">
                Team LEMA
            </div>
            <!-- Tagline -->
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Learning Management System
            </div>
        </div>
    </div>
</template>
