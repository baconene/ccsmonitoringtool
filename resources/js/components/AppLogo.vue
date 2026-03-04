<script setup lang="ts">
// No imports needed for the Team LEMA logo
</script>

<template>
    <div class="flex items-center gap-3">
        <!-- Team LEMA Logo with pink background -->
        <div class="h-12 w-12 rounded-full bg-pink-200 dark:bg-pink-300 p-2 shadow-sm">
            <img 
                src="/images/team-lema-logo.svg" 
                alt="Team LEMA Logo" 
                class="h-full w-full object-contain"
            />
        </div>
        <div class="flex flex-col">
            <!-- Team LEMA Text in pink/magenta -->
            <div class="text-xl font-bold text-pink-600 dark:text-pink-400">
                Team LEMA
            </div>
            <!-- Tagline -->
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Online Learning Management System
            </div>
        </div>
    </div>
</template>
