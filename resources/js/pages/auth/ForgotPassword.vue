<script setup lang="ts">
import PasswordResetLinkController from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle, ArrowLeft, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Forgot password" />
    
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

        <!-- Back to Login Link -->
        <Link
            href="/login"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-2 text-gray-700 hover:text-pink-600 font-bold transition-all duration-300 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg"
        >
            <ArrowLeft class="w-4 h-4" />
            <span class="hidden sm:inline">Back to Login</span>
            <span class="sm:hidden">Back</span>
        </Link>

        <!-- Forgot Password Container -->
        <div class="relative z-10 w-full md:max-w-md px-4 sm:px-0 md:px-6 py-8 md:py-0">
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl md:rounded-3xl border-0 md:border-4 md:border-pink-300 shadow-2xl p-6 sm:p-8 md:p-10 min-h-auto md:min-h-0 flex flex-col justify-center hover:shadow-pink-500/50 transition-shadow duration-500">
                
                <!-- Icon -->
                <div class="flex justify-center mb-6 sm:mb-8">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-gradient-to-br from-pink-100 to-purple-100 border-4 border-pink-300 flex items-center justify-center">
                        <Mail class="w-7 h-7 sm:w-8 sm:h-8 text-pink-600" />
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-800 mb-2">
                        Forgot Password?
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">
                        Enter your email address and we'll send you a link to reset your password
                    </p>
                </div>

                <!-- Status Message -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <div
                        v-if="status"
                        class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border-2 border-green-400 rounded-xl text-center text-xs sm:text-sm font-bold text-green-700"
                    >
                        {{ status }}
                    </div>
                </Transition>

                <!-- Forgot Password Form -->
                <Form
                    v-bind="PasswordResetLinkController.store.form()"
                    :reset-on-success="false"
                    :preserve-state="true"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs sm:text-sm font-bold text-gray-700">
                            Email Address
                        </label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            autofocus
                            placeholder="your.email@example.com"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white border-2 border-pink-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                        />
                        <InputError v-if="errors.email" :message="errors.email" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full py-2.5 sm:py-3 text-sm sm:text-base font-bold bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="w-4 h-4 animate-spin mr-2 inline"
                        />
                        <span v-if="processing">Sending...</span>
                        <span v-else>Send Reset Link</span>
                    </Button>
                </Form>

                <!-- Divider -->
                <div class="my-6 flex items-center gap-4">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="text-xs text-gray-500 font-medium">OR</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center">
                    <p class="text-xs sm:text-sm text-gray-600">
                        Remember your password?
                        <Link
                            href="/login"
                            class="font-bold text-pink-600 hover:text-pink-700 transition-colors"
                        >
                            Sign In
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 px-4 py-4 bg-white/50 backdrop-blur-sm rounded-xl border border-white/30 text-center">
                <p class="text-xs sm:text-sm text-gray-600">
                    <span class="font-medium">Tip:</span> Check your spam folder if you don't receive the email.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}
</style>
