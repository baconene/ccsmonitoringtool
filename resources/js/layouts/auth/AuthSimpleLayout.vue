<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200 p-6 md:p-10 relative overflow-hidden"
    >
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle, #ec4899 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Colorful Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-pink-300/30 via-purple-300/30 to-yellow-300/30"></div>
        
        <!-- Animated Elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-pink-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 right-20 w-48 h-48 bg-purple-400/20 rounded-full blur-3xl"></div>
        
        <div class="w-full max-w-sm relative z-10">
            <div class="flex flex-col gap-8 bg-white/90 backdrop-blur-xl rounded-3xl border-4 border-pink-300 shadow-2xl p-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-3 font-bold"
                    >
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-white shadow-lg ring-4 ring-pink-300">
                            <img src="/images/team-lema-logo.jpg" alt="Team LEMA Logo" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-xl font-black bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">Team LEMA Web Sci</span>
                            <span class="text-xs text-gray-600 font-bold">STEM Career Platform</span>
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-black text-gray-800">{{ title }}</h1>
                        <p class="text-center text-sm text-gray-600 font-medium">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
