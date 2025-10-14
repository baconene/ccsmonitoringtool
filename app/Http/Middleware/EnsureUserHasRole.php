<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user()->load('role');
        
        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            // Redirect to appropriate dashboard based on user role
            $userRoleName = $user->getRoleNameAttribute();
            
            if ($userRoleName === 'student') {
                return redirect()->route('dashboard')->with('error', 'Access denied. Insufficient permissions.');
            }
            
            return redirect()->route('dashboard')->with('error', 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}