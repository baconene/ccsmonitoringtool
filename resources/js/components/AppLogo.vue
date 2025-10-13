<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="flex aspect-square size-10 items-center justify-center">
            <AppLogoIcon class="size-10" />
        </div>
        <svg 
            viewBox="0 0 200 60" 
            class="h-8 w-auto"
            xmlns="http://www.w3.org/2000/svg"
        >
            <defs>
                <linearGradient id="textGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#1E3A8A;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#C41E7A;stop-opacity:1" />
                </linearGradient>
            </defs>
            
            <!-- AstroLearn Text -->
            <text 
                x="2" 
                y="35" 
                font-family="Arial, sans-serif" 
                font-size="32" 
                font-weight="700" 
                fill="url(#textGradient)"
            >
                Astro
            </text>
            <text 
                x="80" 
                y="35" 
                font-family="Arial, sans-serif" 
                font-size="32" 
                font-weight="700" 
                fill="#C41E7A"
            >
                Learn
            </text>
            
            <!-- Tagline -->
            <text 
                x="2" 
                y="52" 
                font-family="Arial, sans-serif" 
                font-size="10" 
                font-weight="400" 
                fill="#64748b"
                letter-spacing="0.5"
            >
                Ignite Your Mind. Explore Universes.
            </text>
        </svg>
    </div>
</template>
