<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Book, Rocket, Sparkles, Brain, GraduationCap } from 'lucide-vue-next';
import ConstellationBackground from '@/components/ConstellationBackground.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <Head title="Team LEMA Web Sci - STEM Career Learning Management System">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="min-h-screen bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200 relative overflow-hidden"
    >
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle, #ec4899 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Colorful Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-pink-300/30 via-purple-300/30 to-yellow-300/30"></div>
        
        <!-- Animated Elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-pink-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 right-20 w-48 h-48 bg-purple-400/20 rounded-full blur-3xl" style="animation: float 8s ease-in-out infinite;"></div>
        <div class="absolute bottom-32 left-1/4 w-40 h-40 bg-yellow-400/20 rounded-full blur-3xl" style="animation: float 6s ease-in-out infinite reverse;"></div>
        
        <!-- Navigation Header -->
        <header class="relative z-10 w-full px-6 py-6 bg-white/60 backdrop-blur-md shadow-lg">
            <nav class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-white shadow-lg ring-2 ring-pink-300">
                        <img src="/images/team-lema-logo.jpg" alt="Team LEMA Logo" class="w-full h-full object-cover" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">Team LEMA Web Sci</span>
                        <span class="text-xs text-gray-700 font-medium">STEM Career Learning Platform</span>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <Link
                        :href="'/documentation'"
                        class="flex items-center gap-2 text-gray-700 hover:text-pink-600 px-4 py-2 rounded-full font-medium transition-colors duration-300"
                    >
                        <Book class="w-4 h-4" />
                        <span>Docs</span>
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="'/dashboard'"
                        class="bg-gradient-to-r from-pink-500 to-purple-500 text-white px-6 py-2 rounded-full font-medium hover:from-pink-600 hover:to-purple-600 transition-all duration-300 shadow-md"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 text-white px-6 py-2 rounded-full font-bold hover:from-pink-600 hover:via-purple-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl"
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
                    <div class="inline-flex items-center px-5 py-2 bg-white/80 backdrop-blur-sm rounded-full text-pink-600 text-sm font-bold border-2 border-pink-300 shadow-lg">
                        <Brain class="w-5 h-5 mr-2 text-purple-600" />
                        Learning • Mastery • Advancement
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                        <span class="bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Ignite Your
                        </span>
                        <br />
                        <span class="text-gray-800">
                            STEM Career
                        </span>
                    </h1>
                    
                    <p class="text-xl text-gray-700 leading-relaxed max-w-2xl font-medium">
                        Team LEMA Web Sci empowers students to explore Science, Technology, Engineering, and Mathematics 
                        through an innovative learning platform designed for the future of STEM careers.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link
                            :href="$page.props.auth.user ? '/dashboard' : login()"
                            class="group bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 text-white px-8 py-4 rounded-full font-black text-lg hover:from-pink-700 hover:via-purple-700 hover:to-blue-700 transition-all duration-300 shadow-2xl hover:shadow-3xl hover:scale-105 text-center flex items-center justify-center gap-3"
                        >
                            <GraduationCap class="w-6 h-6 group-hover:bounce transition-transform" />
                            {{ $page.props.auth.user ? 'Enter Learning Hub' : 'Start Learning' }}
                        </Link>
                        <Link
                            :href="'/documentation'"
                            class="bg-white/80 backdrop-blur-sm text-pink-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-white transition-all duration-300 border-2 border-pink-300 text-center flex items-center justify-center gap-2 shadow-lg"
                        >
                            <Book class="w-5 h-5" />
                            <span>Explore Features</span>
                        </Link>
                    </div>
                </div>
                
                <!-- Hero Visual - Logo Display -->
                <div class="hidden lg:flex relative items-center justify-center">
                    <div class="relative w-full max-w-md">
                        <!-- Main Logo with Glow Effect -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-pink-400 via-purple-400 to-blue-400 rounded-full blur-3xl opacity-50 animate-pulse"></div>
                            <div class="relative rounded-full overflow-hidden bg-white shadow-2xl ring-8 ring-white/50">
                                <img 
                                    src="/images/team-lema-logo.jpg" 
                                    alt="Team LEMA Web Sci Logo" 
                                    class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500"
                                />
                            </div>
                        </div>
                        <!-- Floating Icons -->
                        <div class="absolute -top-10 -right-10 bg-white/90 p-4 rounded-2xl shadow-xl" style="animation: float 3s ease-in-out infinite;">
                            <Brain class="w-8 h-8 text-pink-600" />
                        </div>
                        <div class="absolute -bottom-10 -left-10 bg-white/90 p-4 rounded-2xl shadow-xl" style="animation: float 4s ease-in-out infinite reverse;">
                            <GraduationCap class="w-8 h-8 text-purple-600" />
                        </div>
                        <div class="absolute top-1/2 -right-16 bg-white/90 p-4 rounded-2xl shadow-xl" style="animation: float 5s ease-in-out infinite;">
                            <Sparkles class="w-8 h-8 text-blue-600" />
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Features Section -->
        <section class="relative z-10 py-20 px-6 bg-white/40 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-black text-gray-800 mb-4">
                        Why Choose Team LEMA Web Sci?
                    </h2>
                    <p class="text-gray-700 text-xl max-w-3xl mx-auto font-medium">
                        Experience the next generation of STEM education with features designed for student success
                    </p>
                </div>
                
                <!-- Feature Grid -->
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 border-4 border-pink-300 hover:border-pink-400 hover:shadow-2xl transition-all duration-300 group">
                        <div class="w-14 h-14 bg-gradient-to-r from-pink-500 to-purple-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800 mb-4">STEM-Focused Curriculum</h3>
                        <p class="text-gray-700 font-medium">Master Science, Technology, Engineering, and Mathematics with our comprehensive career-oriented courses.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 border-4 border-purple-300 hover:border-purple-400 hover:shadow-2xl transition-all duration-300 group">
                        <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800 mb-4">Progress Tracking</h3>
                        <p class="text-gray-700 font-medium">Monitor your learning journey with real-time analytics and personalized performance insights.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 border-4 border-blue-300 hover:border-blue-400 hover:shadow-2xl transition-all duration-300 group">
                        <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-800 mb-4">Interactive Learning</h3>
                        <p class="text-gray-700 font-medium">Engage with hands-on projects, quizzes, and collaborative activities designed for STEM mastery.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-4xl mx-auto text-center bg-white/60 backdrop-blur-lg rounded-3xl p-12 shadow-2xl border-4 border-pink-300">
                <h2 class="text-4xl font-black text-gray-800 mb-6">
                    Ready to Ignite Your STEM Career?
                </h2>
                <p class="text-xl text-gray-700 mb-8 max-w-2xl mx-auto font-medium">
                    Join Team LEMA Web Sci and unlock your potential in Science, Technology, Engineering, and Mathematics.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="$page.props.auth.user ? '/dashboard' : login()"
                        class="bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 text-white px-10 py-4 rounded-full font-black text-lg hover:from-pink-700 hover:via-purple-700 hover:to-blue-700 transition-all duration-300 shadow-2xl hover:shadow-3xl hover:scale-105"
                    >
                        {{ $page.props.auth.user ? 'Go to Dashboard' : 'Start Your Journey' }}
                    </Link>
                    <Link
                        :href="'/documentation'"
                        class="bg-white text-pink-600 px-10 py-4 rounded-full font-black text-lg hover:bg-gray-50 transition-all duration-300 border-3 border-pink-300 flex items-center justify-center gap-2 shadow-lg"
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
