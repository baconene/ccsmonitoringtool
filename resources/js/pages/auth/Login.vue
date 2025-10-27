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
    <Head title="Sign In - AstroLearn">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-950 to-indigo-950 relative overflow-hidden flex items-center justify-center"
    >
        <!-- Constellation Background -->
        <ConstellationBackground />
        
        <!-- Cosmic Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-purple-900/20 via-transparent to-pink-900/20"></div>
        
        <!-- Animated Orbs -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 right-20 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl" style="animation: float 8s ease-in-out infinite;"></div>
        <div class="absolute bottom-32 left-1/4 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl" style="animation: float 6s ease-in-out infinite reverse;"></div>

        <!-- Back to Home Link -->
        <Link
            href="/"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-2 text-white/70 hover:text-white font-medium transition-all duration-300 hover:drop-shadow-[0_0_12px_rgba(192,132,252,0.8)] active:drop-shadow-[0_0_20px_rgba(192,132,252,1)]"
        >
            <ArrowLeft class="w-4 h-4" />
            <span>Back to Home</span>
        </Link>

        <!-- Login Container -->
        <div class="relative z-10 w-full md:max-w-md px-0 md:px-6 py-0 md:py-0">
            <div class="bg-white/5 backdrop-blur-xl rounded-none md:rounded-3xl border-0 md:border border-purple-400/30 shadow-2xl p-6 sm:p-8 md:p-10 min-h-screen md:min-h-0 flex flex-col justify-center hover:shadow-purple-500/20 transition-shadow duration-500">
                <!-- Logo -->
                <div class="flex justify-center mb-6 sm:mb-8">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <AppLogoIcon class="w-10 h-10 sm:w-14 sm:h-14" />
                        <div class="flex flex-col">
                            <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">AstroLearn</span>
                            <span class="text-xs text-gray-400">Explore Universes</span>
                        </div>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2 flex items-center justify-center gap-2">
                        <Sparkles class="w-5 h-5 sm:w-6 sm:h-6 text-pink-400" />
                        Welcome Back
                    </h1>
                    <p class="text-sm sm:text-base text-gray-300">Continue your cosmic learning journey</p>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-500/20 border border-green-400/40 rounded-xl text-center text-xs sm:text-sm font-medium text-green-100 backdrop-blur-sm"
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
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-gray-200">
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
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/5 border border-purple-400/30 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all backdrop-blur-sm"
                        />
                        <InputError v-if="errors.email" :message="errors.email" class="text-pink-400 text-xs sm:text-sm" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs sm:text-sm font-semibold text-white">
                                Password
                            </label>
                            <Link
                                v-if="canResetPassword"
                                href="/forgot-password"
                                class="text-xs sm:text-sm text-purple-400 hover:text-purple-300 transition-colors"
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
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/5 border border-purple-400/30 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all backdrop-blur-sm"
                        />
                        <InputError v-if="errors.password" :message="errors.password" class="text-pink-400 text-xs sm:text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            :tabindex="3"
                            class="w-4 h-4 bg-white/5 border-purple-400/30 rounded focus:ring-2 focus:ring-purple-500 text-purple-500"
                        />
                        <label for="remember" class="ml-3 text-xs sm:text-sm text-gray-300">
                            Remember me for 30 days
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        class="w-full bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg hover:from-purple-700 hover:via-pink-700 hover:to-indigo-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-purple-500/50 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        data-test="login-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="h-5 w-5 animate-spin"
                        />
                        <span v-else>Launch Into Learning</span>
                    </button>
                </Form>

                <!-- Documentation Link -->
                <div class="mt-6 sm:mt-8 text-center">
                    <Link
                        href="/documentation"
                        class="inline-flex items-center gap-2 text-gray-400 hover:text-purple-400 text-xs sm:text-sm transition-colors"
                    >
                        <Sparkles class="w-3 h-3 sm:w-4 sm:h-4" />
                        <span>Explore Documentation</span>
                    </Link>
                </div>
            </div>

            <!-- Additional Info -->
            <p class="text-center text-gray-500 text-xs sm:text-sm mt-auto pt-6 md:mt-4 md:pt-0 px-4">
                © 2025 AstroLearn. Ignite Your Mind. Explore Universes.
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
