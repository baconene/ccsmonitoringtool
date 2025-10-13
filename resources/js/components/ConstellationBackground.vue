<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

interface Node {
    x: number;
    y: number;
    vx: number;
    vy: number;
    radius: number;
    isHovered: boolean;
    rotation: number;
    rotationSpeed: number;
}

interface Connection {
    from: Node;
    to: Node;
    opacity: number;
}

interface Comet {
    x: number;
    y: number;
    vx: number;
    vy: number;
    life: number;
    maxLife: number;
    size: number;
}

const canvasRef = ref<HTMLCanvasElement | null>(null);
let animationFrameId: number;
let nodes: Node[] = [];
let connections: Connection[] = [];
let comets: Comet[] = [];
let mouseX = -1000;
let mouseY = -1000;
let prevMouseX = -1000;
let prevMouseY = -1000;
let canvas: HTMLCanvasElement;
let ctx: CanvasRenderingContext2D;
let isMouseMoving = false;
let mouseTimer: number;

const nodeCount = 80;
const connectionDistance = 150;
const hoverDistance = 100;

// Initialize nodes
const initNodes = () => {
    if (!canvas) return;
    nodes = [];
    for (let i = 0; i < nodeCount; i++) {
        nodes.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5,
            radius: Math.random() * 2 + 1,
            isHovered: false,
            rotation: 0,
            rotationSpeed: (Math.random() - 0.5) * 0.1
        });
    }
};

// Set canvas size
const resizeCanvas = () => {
    if (!canvas) return;
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    initNodes();
};

// Mouse move handler - track on window, not just canvas
const handleMouseMove = (e: MouseEvent) => {
    prevMouseX = mouseX;
    prevMouseY = mouseY;
    
    // Get mouse position relative to viewport (which is what we need since canvas is fullscreen)
    mouseX = e.clientX;
    mouseY = e.clientY;
    
    isMouseMoving = true;
    clearTimeout(mouseTimer);
    mouseTimer = window.setTimeout(() => {
        isMouseMoving = false;
    }, 100);
    
    // Create comet on mouse move
    if (prevMouseX !== -1000 && Math.random() > 0.5) { // Increased chance to 50%
        const dx = mouseX - prevMouseX;
        const dy = mouseY - prevMouseY;
        const speed = Math.sqrt(dx * dx + dy * dy);
        
        if (speed > 5) { // Only create comets on faster movements
            comets.push({
                x: mouseX,
                y: mouseY,
                vx: dx * 0.5,
                vy: dy * 0.5,
                life: 45,
                maxLife: 45,
                size: Math.min(speed * 0.15, 4)
            });
        }
    }
};

onMounted(() => {
    if (!canvasRef.value) return;
    
    canvas = canvasRef.value;
    const context = canvas.getContext('2d');
    if (!context) return;
    ctx = context;

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    // Listen to window for mouse events since canvas is fullscreen
    window.addEventListener('mousemove', handleMouseMove);
    
    console.log('ConstellationBackground mounted:', {
        canvasWidth: canvas.width,
        canvasHeight: canvas.height,
        nodeCount: nodes.length
    });

    // Update connections
    const updateConnections = () => {
        connections = [];
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < connectionDistance) {
                    connections.push({
                        from: nodes[i],
                        to: nodes[j],
                        opacity: 1 - distance / connectionDistance
                    });
                }
            }
        }
    };

    // Animation loop
    let frameCount = 0;
    const animate = () => {
        if (!ctx || !canvas) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        frameCount++;
        let hoveredCount = 0;
        
        // Update and draw nodes
        nodes.forEach(node => {
            // Check hover
            const dx = mouseX - node.x;
            const dy = mouseY - node.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            node.isHovered = distance < hoverDistance;
            
            if (node.isHovered) hoveredCount++;
            
            // Debug logging every 60 frames
            if (frameCount % 60 === 0 && hoveredCount === 1) {
                console.log(`Mouse: (${mouseX.toFixed(0)}, ${mouseY.toFixed(0)}) | Node: (${node.x.toFixed(0)}, ${node.y.toFixed(0)}) | Distance: ${distance.toFixed(0)} | Hovered: ${node.isHovered}`);
            }

            // Update position
            node.x += node.vx;
            node.y += node.vy;

            // Bounce off edges
            if (node.x < 0 || node.x > canvas.width) node.vx *= -1;
            if (node.y < 0 || node.y > canvas.height) node.vy *= -1;

            // Apply hover effects
            if (node.isHovered) {
                node.rotation += node.rotationSpeed * 5;
                // Move away from cursor
                const angle = Math.atan2(dy, dx);
                node.x -= Math.cos(angle) * 2;
                node.y -= Math.sin(angle) * 2;
            } else {
                node.rotation += node.rotationSpeed;
            }

            // Draw node
            ctx.save();
            ctx.translate(node.x, node.y);
            ctx.rotate(node.rotation);
            
            // Create gradient
            const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, node.radius * 3);
            if (node.isHovered) {
                gradient.addColorStop(0, 'rgba(233, 30, 140, 0.9)');
                gradient.addColorStop(0.5, 'rgba(196, 30, 122, 0.6)');
                gradient.addColorStop(1, 'rgba(196, 30, 122, 0)');
                
                // Draw glow
                ctx.beginPath();
                ctx.arc(0, 0, node.radius * 4, 0, Math.PI * 2);
                ctx.fillStyle = gradient;
                ctx.fill();
            }
            
            // Draw star shape
            if (node.isHovered) {
                const spikes = 5;
                const outerRadius = node.radius * 2;
                const innerRadius = node.radius;
                
                ctx.beginPath();
                for (let i = 0; i < spikes * 2; i++) {
                    const radius = i % 2 === 0 ? outerRadius : innerRadius;
                    const angle = (Math.PI / spikes) * i;
                    const x = Math.cos(angle) * radius;
                    const y = Math.sin(angle) * radius;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.closePath();
                ctx.fillStyle = '#E91E8C';
                ctx.fill();
            } else {
                // Draw circle
                ctx.beginPath();
                ctx.arc(0, 0, node.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(196, 30, 122, 0.6)';
                ctx.fill();
            }
            
            ctx.restore();
        });

        // Update and draw connections
        updateConnections();
        connections.forEach(conn => {
            ctx.beginPath();
            ctx.moveTo(conn.from.x, conn.from.y);
            ctx.lineTo(conn.to.x, conn.to.y);
            
            const gradient = ctx.createLinearGradient(
                conn.from.x, conn.from.y,
                conn.to.x, conn.to.y
            );
            gradient.addColorStop(0, `rgba(30, 58, 138, ${conn.opacity * 0.15})`);
            gradient.addColorStop(0.5, `rgba(196, 30, 122, ${conn.opacity * 0.25})`);
            gradient.addColorStop(1, `rgba(30, 58, 138, ${conn.opacity * 0.15})`);
            
            ctx.strokeStyle = gradient;
            ctx.lineWidth = conn.opacity * 1.5;
            ctx.stroke();
        });

        // Update and draw comets
        comets = comets.filter(comet => {
            comet.x += comet.vx;
            comet.y += comet.vy;
            comet.vx *= 0.98; // Friction
            comet.vy *= 0.98;
            comet.life--;
            
            if (comet.life <= 0) return false;
            
            // Draw comet
            const opacity = comet.life / comet.maxLife;
            
            // Comet trail
            ctx.save();
            const trailLength = 20;
            const gradient = ctx.createLinearGradient(
                comet.x, comet.y,
                comet.x - comet.vx * 3, comet.y - comet.vy * 3
            );
            gradient.addColorStop(0, `rgba(233, 30, 140, ${opacity * 0.8})`);
            gradient.addColorStop(0.5, `rgba(196, 30, 122, ${opacity * 0.4})`);
            gradient.addColorStop(1, `rgba(196, 30, 122, 0)`);
            
            ctx.beginPath();
            ctx.moveTo(comet.x, comet.y);
            ctx.lineTo(comet.x - comet.vx * 3, comet.y - comet.vy * 3);
            ctx.strokeStyle = gradient;
            ctx.lineWidth = comet.size;
            ctx.lineCap = 'round';
            ctx.stroke();
            
            // Comet head
            const headGradient = ctx.createRadialGradient(
                comet.x, comet.y, 0,
                comet.x, comet.y, comet.size * 2
            );
            headGradient.addColorStop(0, `rgba(255, 255, 255, ${opacity})`);
            headGradient.addColorStop(0.3, `rgba(233, 30, 140, ${opacity * 0.8})`);
            headGradient.addColorStop(1, `rgba(196, 30, 122, 0)`);
            
            ctx.beginPath();
            ctx.arc(comet.x, comet.y, comet.size * 2, 0, Math.PI * 2);
            ctx.fillStyle = headGradient;
            ctx.fill();
            
            ctx.restore();
            
            return true;
        });

        animationFrameId = requestAnimationFrame(animate);
    };

    initNodes();
    animate();
});

// Cleanup on unmount
onUnmounted(() => {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
    if (mouseTimer) {
        clearTimeout(mouseTimer);
    }
    window.removeEventListener('resize', resizeCanvas);
    window.removeEventListener('mousemove', handleMouseMove);
});
</script>

<template>
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <canvas 
            ref="canvasRef" 
            class="absolute inset-0 w-full h-full opacity-40 dark:opacity-30 pointer-events-auto"
        />
    </div>
</template>
