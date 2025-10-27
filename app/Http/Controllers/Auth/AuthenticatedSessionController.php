<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        \Log::info('Login page accessed', [
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'has_session' => $request->hasSession(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        \Log::info('Login attempt started', [
            'email' => $request->input('email'),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'request_csrf' => $request->header('X-CSRF-TOKEN'),
            'has_session' => $request->hasSession(),
            'ip' => $request->ip(),
        ]);

        try {
            $user = $request->validateCredentials();

            \Log::info('Credentials validated', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            if (Features::enabled(Features::twoFactorAuthentication()) && $user->hasEnabledTwoFactorAuthentication()) {
                $request->session()->put([
                    'login.id' => $user->getKey(),
                    'login.remember' => $request->boolean('remember'),
                ]);

                \Log::info('Two-factor authentication required', ['user_id' => $user->id]);
                return to_route('two-factor.login');
            }

            Auth::login($user, $request->boolean('remember'));
            \Log::info('User logged in', ['user_id' => $user->id]);

            $request->session()->regenerate();
            \Log::info('Session regenerated', [
                'new_session_id' => session()->getId(),
                'new_csrf_token' => csrf_token(),
            ]);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            \Log::error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        \Log::info('Logout attempt started', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'request_csrf' => $request->header('X-CSRF-TOKEN'),
            'has_session' => $request->hasSession(),
            'ip' => $request->ip(),
        ]);

        try {
            Auth::guard('web')->logout();
            \Log::info('User logged out', ['session_id' => session()->getId()]);

            $request->session()->invalidate();
            \Log::info('Session invalidated');

            $request->session()->regenerateToken();
            \Log::info('Token regenerated', ['new_csrf_token' => csrf_token()]);

            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
