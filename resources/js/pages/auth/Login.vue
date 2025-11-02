<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle, ArrowLeft, Sparkles } from 'lucide-vue-next';
import ConstellationBackground from '@/components/ConstellationBackground.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

// Force full page reload after successful login to get new CSRF token
const handleLoginSuccess = () => {
    window.location.href = '/dashboard';
};
</script>

<template>
    <Head title="Sign In - Team LEMA Web Sci">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div
        class="min-h-screen bg-gradient-to-br from-pink-100 via-purple-100 to-pink-200 relative overflow-hidden flex items-center justify-center"
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

        <!-- Back to Home Link -->
        <Link
            href="/"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-2 text-gray-700 hover:text-pink-600 font-bold transition-all duration-300 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg"
        >
            <ArrowLeft class="w-4 h-4" />
            <span>Back to Home</span>
        </Link>

        <!-- Login Container -->
        <div class="relative z-10 w-full md:max-w-md px-0 md:px-6 py-0 md:py-0">
            <div class="bg-white/90 backdrop-blur-xl rounded-none md:rounded-3xl border-0 md:border-4 md:border-pink-300 shadow-2xl p-6 sm:p-8 md:p-10 min-h-screen md:min-h-0 flex flex-col justify-center hover:shadow-pink-500/50 transition-shadow duration-500">
                <!-- Logo -->
                <div class="flex justify-center mb-6 sm:mb-8">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-white shadow-lg ring-4 ring-pink-300">
                            <img src="/images/team-lema-logo.jpg" alt="Team LEMA Logo" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl sm:text-2xl font-black bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">Team LEMA</span>
                            <span class="text-xs text-gray-600 font-bold">Web Sci</span>
                        </div>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-800 mb-2 flex items-center justify-center gap-2">
                        <Sparkles class="w-5 h-5 sm:w-6 sm:h-6 text-pink-500" />
                        Welcome Back
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600 font-medium">Continue your STEM learning journey</p>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border-2 border-green-400 rounded-xl text-center text-xs sm:text-sm font-bold text-green-700"
                >
                    {{ status }}
                </div>

                <!-- Login Form -->
                <Form
                    v-bind="AuthenticatedSessionController.store.form()"
                    :reset-on-success="['password']"
                    :preserve-state="false"
                    :preserve-scroll="false"
                    @success="handleLoginSuccess"
                    v-slot="{ errors, processing }"
                    class="space-y-4 sm:space-y-6"
                >
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs sm:text-sm font-bold text-gray-700">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="your.email@example.com"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white border-2 border-pink-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                        />
                        <InputError v-if="errors.email" :message="errors.email" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs sm:text-sm font-bold text-gray-700">
                                Password
                            </label>
                            <Link
                                v-if="canResetPassword"
                                href="/forgot-password"
                                class="text-xs sm:text-sm text-pink-600 hover:text-pink-700 font-medium transition-colors"
                                :tabindex="5"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white border-2 border-pink-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                        />
                        <InputError v-if="errors.password" :message="errors.password" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            :tabindex="3"
                            class="w-4 h-4 border-2 border-pink-400 rounded focus:ring-2 focus:ring-pink-500 text-pink-600"
                        />
                        <label for="remember" class="ml-3 text-xs sm:text-sm text-gray-700 font-medium">
                            Remember me for 30 days
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        class="w-full bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-black text-base sm:text-lg hover:from-pink-700 hover:via-purple-700 hover:to-blue-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-pink-500/50 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        data-test="login-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="h-5 w-5 animate-spin"
                        />
                        <span v-else>Start Learning</span>
                    </button>
                </Form>

                <!-- Documentation Link -->
                <div class="mt-6 sm:mt-8 text-center">
                    <Link
                        href="/documentation"
                        class="inline-flex items-center gap-2 text-gray-600 hover:text-pink-600 text-xs sm:text-sm transition-colors font-medium"
                    >
                        <Sparkles class="w-3 h-3 sm:w-4 sm:h-4" />
                        <span>Explore Documentation</span>
                    </Link>
                </div>
            </div>

            <!-- Additional Info -->
            <p class="text-center text-gray-700 text-xs sm:text-sm mt-auto pt-6 md:mt-4 md:pt-0 px-4 font-medium">
                © 2025 Team LEMA Web Sci. STEM Career Learning Platform.
            </p>
        </div>
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
