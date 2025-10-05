<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Book } from 'lucide-vue-next';
</script>

<template>
    <Head title="EduFlow - Transform Your Learning Experience">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 relative overflow-hidden"
        style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80'); background-size: cover; background-position: center;"
    >
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/90 via-purple-900/85 to-pink-800/90"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute top-40 right-20 w-32 h-32 bg-pink-400/20 rounded-full blur-2xl animate-bounce delay-1000"></div>
        <div class="absolute bottom-32 left-1/4 w-24 h-24 bg-blue-400/20 rounded-full blur-xl animate-pulse delay-500"></div>
        <!-- Navigation Header -->
        <header class="relative z-10 w-full px-6 py-6">
            <nav class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-violet-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">Bacon Edu</span>
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
                    <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/90 text-sm font-medium border border-white/20">
                        🚀 Revolutionary Learning Platform
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-tight">
                        Transform Your 
                        <span class="bg-gradient-to-r from-pink-400 to-violet-400 bg-clip-text text-transparent">
                            Learning
                        </span>
                        <br />Experience
                    </h1>
                    
                    <p class="text-xl text-white/80 leading-relaxed max-w-2xl">
                        Unlock the future of education with our cutting-edge Learning Management System. 
                        Designed for modern learners and innovative instructors.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link
                            :href="$page.props.auth.user ? '/dashboard' : login()"
                            class="bg-gradient-to-r from-pink-500 to-violet-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-pink-600 hover:to-violet-600 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 text-center"
                        >
                            {{ $page.props.auth.user ? 'Go to Dashboard' : 'Sign In' }}
                        </Link>
                        <Link
                            :href="'/documentation'"
                            class="bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300 border-2 border-white/30 text-center flex items-center justify-center gap-2"
                        >
                            <Book class="w-5 h-5" />
                            <span>View Documentation</span>
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
                        Why Choose Bacon Edu?
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
