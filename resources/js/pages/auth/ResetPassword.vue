<script setup lang="ts">
import NewPasswordController from '@/actions/App/Http/Controllers/Auth/NewPasswordController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle, ArrowLeft, Key } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Reset password" />
    
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

        <!-- Reset Password Container -->
        <div class="relative z-10 w-full md:max-w-md px-4 sm:px-0 md:px-6 py-8 md:py-0">
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl md:rounded-3xl border-0 md:border-4 md:border-pink-300 shadow-2xl p-6 sm:p-8 md:p-10 min-h-auto md:min-h-0 flex flex-col justify-center hover:shadow-pink-500/50 transition-shadow duration-500">
                
                <!-- Icon -->
                <div class="flex justify-center mb-6 sm:mb-8">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-gradient-to-br from-pink-100 to-purple-100 border-4 border-pink-300 flex items-center justify-center">
                        <Key class="w-7 h-7 sm:w-8 sm:h-8 text-pink-600" />
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-800 mb-2">
                        Reset Password
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">
                        Create a new, strong password for your account
                    </p>
                </div>

                <!-- Reset Password Form -->
                <Form
                    v-bind="NewPasswordController.store.form()"
                    :transform="(data) => ({ ...data, token: token, email: inputEmail })"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Email Field (Read-only) -->
                    <div class="space-y-2">
                        <Label for="email">Email Address</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            v-model="inputEmail"
                            readonly
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-100 border-2 border-gray-300 rounded-xl text-gray-600 placeholder-gray-400 focus:outline-none cursor-not-allowed"
                        />
                        <InputError v-if="errors.email" :message="errors.email" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <Label for="password">New Password</Label>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            autofocus
                            placeholder="••••••••"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white border-2 border-pink-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                        />
                        <InputError v-if="errors.password" :message="errors.password" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="space-y-2">
                        <Label for="password_confirmation">Confirm Password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white border-2 border-pink-300 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all"
                        />
                        <InputError v-if="errors.password_confirmation" :message="errors.password_confirmation" class="text-pink-600 text-xs sm:text-sm font-medium" />
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        class="w-full py-2.5 sm:py-3 text-sm sm:text-base font-bold bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed mt-4"
                        :disabled="processing"
                        data-test="reset-password-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="w-4 h-4 animate-spin mr-2 inline"
                        />
                        <span v-if="processing">Updating Password...</span>
                        <span v-else>Reset Password</span>
                    </Button>
                </Form>

                <!-- Security Tips -->
                <div class="mt-6 p-3 sm:p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <p class="text-xs sm:text-sm text-blue-800 font-medium">
                        <strong>Security Tip:</strong> Use a strong password with uppercase, lowercase, numbers, and symbols.
                    </p>
                </div>
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
