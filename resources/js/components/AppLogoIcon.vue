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
        viewBox="0 0 200 200"
        :class="className"
        v-bind="$attrs"
        >
        <defs>
            <linearGradient id="petalGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" style="stop-color:#C41E7A;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#E91E8C;stop-opacity:1" />
            </linearGradient>
            <linearGradient id="navyGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" style="stop-color:#1E3A8A;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#2E4B9E;stop-opacity:1" />
            </linearGradient>
        </defs>
        
        <!-- Outer orbital ring -->
        <ellipse 
            cx="100" cy="100" 
            rx="85" ry="30" 
            fill="none" 
            stroke="url(#petalGradient)" 
            stroke-width="2.5"
            transform="rotate(-25 100 100)"
            opacity="0.7"
        />
        
        <!-- Outer dots on orbit -->
        <circle cx="170" cy="85" r="2.5" fill="#C41E7A" opacity="0.8"/>
        <circle cx="30" cy="115" r="2.5" fill="#C41E7A" opacity="0.8"/>
        <circle cx="145" cy="65" r="2" fill="#E91E8C" opacity="0.6"/>
        <circle cx="55" cy="135" r="2" fill="#E91E8C" opacity="0.6"/>
        
        <!-- Inner orbital ring -->
        <circle 
            cx="100" cy="100" 
            r="60" 
            fill="none" 
            stroke="url(#navyGradient)" 
            stroke-width="1.5"
            opacity="0.6"
        />
        
        <!-- Navy petals (background) -->
        <g transform="translate(100, 100)">
            <path d="M 0,-35 Q 8,-25 0,-15 Q -8,-25 0,-35" fill="url(#navyGradient)" transform="rotate(45)"/>
            <path d="M 0,-35 Q 8,-25 0,-15 Q -8,-25 0,-35" fill="url(#navyGradient)" transform="rotate(135)"/>
            <path d="M 0,-35 Q 8,-25 0,-15 Q -8,-25 0,-35" fill="url(#navyGradient)" transform="rotate(225)"/>
            <path d="M 0,-35 Q 8,-25 0,-15 Q -8,-25 0,-35" fill="url(#navyGradient)" transform="rotate(315)"/>
        </g>
        
        <!-- Pink/Magenta petals (foreground) -->
        <g transform="translate(100, 100)">
            <path d="M 0,-40 Q 10,-28 0,-16 Q -10,-28 0,-40" fill="url(#petalGradient)" transform="rotate(0)">
                <animate attributeName="opacity" values="1;0.8;1" dur="3s" repeatCount="indefinite"/>
            </path>
            <path d="M 0,-40 Q 10,-28 0,-16 Q -10,-28 0,-40" fill="url(#petalGradient)" transform="rotate(90)">
                <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" repeatCount="indefinite"/>
            </path>
            <path d="M 0,-40 Q 10,-28 0,-16 Q -10,-28 0,-40" fill="url(#petalGradient)" transform="rotate(180)">
                <animate attributeName="opacity" values="1;0.8;1" dur="3s" repeatCount="indefinite"/>
            </path>
            <path d="M 0,-40 Q 10,-28 0,-16 Q -10,-28 0,-40" fill="url(#petalGradient)" transform="rotate(270)">
                <animate attributeName="opacity" values="0.8;1;0.8" dur="3s" repeatCount="indefinite"/>
            </path>
        </g>
        
        <!-- Inner mandala pattern -->
        <g transform="translate(100, 100)">
            <circle r="12" fill="none" stroke="#E91E8C" stroke-width="1.5"/>
            <circle r="8" fill="none" stroke="#C41E7A" stroke-width="1"/>
            <circle r="3" fill="#E91E8C">
                <animate attributeName="r" values="3;4;3" dur="2s" repeatCount="indefinite"/>
            </circle>
        </g>
        
        <!-- Small decorative dots around the flower -->
        <circle cx="100" cy="40" r="1.5" fill="#E91E8C" opacity="0.6"/>
        <circle cx="160" cy="100" r="1.5" fill="#C41E7A" opacity="0.6"/>
        <circle cx="100" cy="160" r="1.5" fill="#E91E8C" opacity="0.6"/>
        <circle cx="40" cy="100" r="1.5" fill="#C41E7A" opacity="0.6"/>
    </svg>
</template>
