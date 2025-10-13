<script setup lang="ts">
import { onMounted, ref } from 'vue';

const canvasRef = ref<HTMLCanvasElement | null>(null);

interface Star {
    x: number;
    y: number;
    radius: number;
    opacity: number;
    speed: number;
}

onMounted(() => {
    if (!canvasRef.value) return;
    
    const canvas = canvasRef.value;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Set canvas size
    const resizeCanvas = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Create stars
    const stars: Star[] = [];
    const starCount = 150;
    
    for (let i = 0; i < starCount; i++) {
        stars.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 1.5 + 0.5,
            opacity: Math.random() * 0.5 + 0.3,
            speed: Math.random() * 0.2 + 0.1
        });
    }

    // Animation loop
    const animate = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw stars with twinkling effect
        stars.forEach(star => {
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(196, 30, 122, ${star.opacity})`;
            ctx.fill();
            
            // Twinkling effect
            star.opacity += star.speed * (Math.random() > 0.5 ? 1 : -1);
            if (star.opacity > 0.8) star.opacity = 0.8;
            if (star.opacity < 0.1) star.opacity = 0.1;
        });

        requestAnimationFrame(animate);
    };

    animate();

    // Cleanup
    return () => {
        window.removeEventListener('resize', resizeCanvas);
    };
});
</script>

<template>
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <canvas 
            ref="canvasRef" 
            class="absolute inset-0 opacity-30 dark:opacity-20"
        />
        
        <!-- Cosmic decorative elements -->
        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="cosmicGlow1">
                    <stop offset="0%" style="stop-color:#C41E7A;stop-opacity:0.15" />
                    <stop offset="100%" style="stop-color:#C41E7A;stop-opacity:0" />
                </radialGradient>
                <radialGradient id="cosmicGlow2">
                    <stop offset="0%" style="stop-color:#1E3A8A;stop-opacity:0.15" />
                    <stop offset="100%" style="stop-color:#1E3A8A;stop-opacity:0" />
                </radialGradient>
            </defs>
            
            <!-- Floating orbs -->
            <circle cx="10%" cy="20%" r="150" fill="url(#cosmicGlow1)">
                <animate attributeName="cy" values="20%;25%;20%" dur="8s" repeatCount="indefinite" />
            </circle>
            <circle cx="85%" cy="70%" r="200" fill="url(#cosmicGlow2)">
                <animate attributeName="cy" values="70%;65%;70%" dur="10s" repeatCount="indefinite" />
            </circle>
            <circle cx="50%" cy="50%" r="100" fill="url(#cosmicGlow1)">
                <animate attributeName="r" values="100;120;100" dur="6s" repeatCount="indefinite" />
            </circle>
        </svg>
    </div>
</template>
