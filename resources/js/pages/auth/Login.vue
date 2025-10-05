<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle, Book, ArrowLeft } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div
        class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 relative overflow-hidden flex items-center justify-center"
        style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80'); background-size: cover; background-position: center;"
    >
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/90 via-purple-900/85 to-pink-800/90"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute top-40 right-20 w-32 h-32 bg-pink-400/20 rounded-full blur-2xl animate-bounce delay-1000"></div>
        <div class="absolute bottom-32 left-1/4 w-24 h-24 bg-blue-400/20 rounded-full blur-xl animate-pulse delay-500"></div>

        <!-- Back to Home Link -->
        <Link
            href="/"
            class="absolute top-6 left-6 z-20 flex items-center gap-2 text-white/90 hover:text-white px-4 py-2 rounded-full font-medium transition-colors duration-300 bg-white/10 backdrop-blur-sm hover:bg-white/20"
        >
            <ArrowLeft class="w-4 h-4" />
            <span>Back to Home</span>
        </Link>

        <!-- Login Container -->
        <div class="relative z-10 w-full max-w-md px-6">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 shadow-2xl p-8 md:p-10">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-violet-500 rounded-xl flex items-center justify-center">
                            <Book class="w-7 h-7 text-white" />
                        </div>
                        <span class="text-2xl font-bold text-white">Bacon Edu</span>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
                    <p class="text-white/80">Sign in to continue your learning journey</p>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-6 p-4 bg-green-500/20 border border-green-400/30 rounded-xl text-center text-sm font-medium text-green-100"
                >
                    {{ status }}
                </div>

                <!-- Login Form -->
                <Form
                    v-bind="AuthenticatedSessionController.store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-white">
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
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                        />
                        <InputError v-if="errors.email" :message="errors.email" class="text-pink-300 text-sm" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-semibold text-white">
                                Password
                            </label>
                            <Link
                                v-if="canResetPassword"
                                href="/forgot-password"
                                class="text-sm text-pink-300 hover:text-pink-200 transition-colors"
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
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                        />
                        <InputError v-if="errors.password" :message="errors.password" class="text-pink-300 text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            :tabindex="3"
                            class="w-4 h-4 bg-white/10 border-white/20 rounded focus:ring-2 focus:ring-pink-500 text-pink-500"
                        />
                        <label for="remember" class="ml-3 text-sm text-white/90">
                            Remember me for 30 days
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        class="w-full bg-gradient-to-r from-pink-500 to-violet-500 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-pink-600 hover:to-violet-600 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        data-test="login-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="h-5 w-5 animate-spin"
                        />
                        <span v-else>Sign In</span>
                    </button>
                </Form>

                <!-- Documentation Link -->
                <div class="mt-8 text-center">
                    <Link
                        href="/documentation"
                        class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm transition-colors"
                    >
                        <Book class="w-4 h-4" />
                        <span>View Documentation</span>
                    </Link>
                </div>
            </div>

            <!-- Additional Info -->
            <p class="text-center text-white/60 text-sm mt-6">
                © 2025 Bacon Edu. All rights reserved.
            </p>
        </div>
    </div>
</template>
