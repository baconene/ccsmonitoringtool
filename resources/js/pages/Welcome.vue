<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Book, Rocket, Sparkles } from 'lucide-vue-next';
import ConstellationBackground from '@/components/ConstellationBackground.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <Head title="AstroLearn - Ignite Your Mind. Explore Universes.">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-950 to-indigo-950 relative overflow-hidden"
    >
        <!-- Constellation Background -->
        <ConstellationBackground />
        
        <!-- Cosmic Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-purple-900/20 via-transparent to-pink-900/20"></div>
        
        <!-- Animated Orbs -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 right-20 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl" style="animation: float 8s ease-in-out infinite;"></div>
        <div class="absolute bottom-32 left-1/4 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl" style="animation: float 6s ease-in-out infinite reverse;"></div>
        <!-- Navigation Header -->
        <header class="relative z-10 w-full px-6 py-6">
            <nav class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <AppLogoIcon class="w-12 h-12" />
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">AstroLearn</span>
                        <span class="text-xs text-gray-400">Explore Universes</span>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <Link
                        :href="'/documentation'"
                        class="flex items-center gap-2 text-white/90 hover:text-white px-4 py-2 rounded-full font-medium transition-colors duration-300"
                    >
                        <Book class="w-4 h-4" />
                        <span>Docs</span>
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="'/dashboard'"
                        class="bg-white/10 backdrop-blur-sm text-white px-6 py-2 rounded-full font-medium hover:bg-white/20 transition-all duration-300 border border-white/20"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="bg-gradient-to-r from-pink-500 to-violet-500 text-white px-6 py-2 rounded-full font-medium hover:from-pink-600 hover:to-violet-600 transition-all duration-300 shadow-lg hover:shadow-xl"
                    >
                        Log in
                    </Link>
                </div>
            </nav>
        </header>
        <!-- Hero Section -->
        <main class="relative z-10 flex-1 flex items-center justify-center px-6 py-20">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center lg:text-left space-y-8">
                    <div class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-purple-500/20 to-pink-500/20 backdrop-blur-sm rounded-full text-white/90 text-sm font-medium border border-purple-400/30 shadow-lg">
                        <Sparkles class="w-4 h-4 mr-2 text-pink-400" />
                        Ignite Your Mind. Explore Universes.
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-tight">
                        Journey Through
                        <br />
                        <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-400 bg-clip-text text-transparent">
                            Infinite Knowledge
                        </span>
                    </h1>
                    
                    <p class="text-xl text-gray-300 leading-relaxed max-w-2xl">
                        Embark on a cosmic learning adventure. AstroLearn transforms education into 
                        an exploration of limitless possibilities, connecting minds across the universe of knowledge.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link
                            :href="$page.props.auth.user ? '/dashboard' : login()"
                            class="group bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-purple-700 hover:via-pink-700 hover:to-indigo-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 text-center flex items-center justify-center gap-2"
                        >
                            <Rocket class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                            {{ $page.props.auth.user ? 'Launch Dashboard' : 'Begin Journey' }}
                        </Link>
                        <Link
                            :href="'/documentation'"
                            class="bg-white/5 backdrop-blur-sm text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all duration-300 border-2 border-purple-400/30 text-center flex items-center justify-center gap-2"
                        >
                            <Book class="w-5 h-5" />
                            <span>Documentation</span>
                        </Link>
                    </div>
                </div>
                
                <!-- Hero Visual -->
                <div class="hidden lg:block relative">
                    <div class="relative w-full h-96">
                        <!-- Dashboard Preview Mockup -->
                        <div class="absolute inset-0 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20 shadow-2xl transform rotate-3 hover:rotate-6 transition-transform duration-500">
                            <div class="p-6 space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 bg-white/20 rounded w-3/4"></div>
                                    <div class="h-4 bg-white/15 rounded w-1/2"></div>
                                    <div class="grid grid-cols-2 gap-4 mt-6">
                                        <div class="h-20 bg-gradient-to-br from-pink-400/20 to-violet-400/20 rounded-xl"></div>
                                        <div class="h-20 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-xl"></div>
                                    </div>
                                    <div class="space-y-2 mt-4">
                                        <div class="h-3 bg-white/10 rounded w-full"></div>
                                        <div class="h-3 bg-white/10 rounded w-4/5"></div>
                                        <div class="h-3 bg-white/10 rounded w-3/5"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Features Section -->
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-white mb-4">
                        Why Choose Astro Learn?
                    </h2>
                    <p class="text-white/80 text-xl max-w-3xl mx-auto">
                        Experience the next generation of learning with features designed for success
                    </p>
                </div>
                
                <!-- Feature Grid -->
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-violet-500 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Smart Course Management</h3>
                        <p class="text-white/80">Create, organize, and deliver engaging courses with our intuitive content management system.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Real-time Analytics</h3>
                        <p class="text-white/80">Track student progress, engagement, and performance with comprehensive analytics dashboards.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Interactive Learning</h3>
                        <p class="text-white/80">Engage students with interactive lessons, quizzes, and collaborative learning experiences.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Ready to Transform Education?
                </h2>
                <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                    Join thousands of educators and students who are already experiencing the future of learning.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="$page.props.auth.user ? '/dashboard' : login()"
                        class="bg-gradient-to-r from-pink-500 to-violet-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-pink-600 hover:to-violet-600 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105"
                    >
                        {{ $page.props.auth.user ? 'Access Your Dashboard' : 'Sign In Now' }}
                    </Link>
                    <Link
                        :href="'/documentation'"
                        class="bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300 border-2 border-white/30 flex items-center justify-center gap-2"
                    >
                        <Book class="w-5 h-5" />
                        <span>Learn More</span>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-20px) scale(1.05);
    }
}
</style>
