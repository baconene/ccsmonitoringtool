<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log POST, PUT, PATCH, DELETE requests and errors
        $shouldLogRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);

        if ($shouldLogRequest) {
            \Log::info('Request received', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token(),
                'has_session' => $request->hasSession(),
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }

        $response = $next($request);

        // Log all error responses (4xx, 5xx)
        if ($response->getStatusCode() >= 400) {
            \Log::error('Error response', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'status' => $response->getStatusCode(),
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token(),
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
            ]);
        }

        return $response;
    }
}
