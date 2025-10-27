<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Http\Request;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            \Log::error('CSRF Token Mismatch (419 Error)', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'session_id' => session()->getId(),
                'session_token' => $request->session()->token(),
                'csrf_token_from_session' => csrf_token(),
                'csrf_token_from_header' => $request->header('X-CSRF-TOKEN'),
                'csrf_token_from_input' => $request->input('_token'),
                'referer' => $request->header('referer'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'cookies' => $request->cookies->all(),
            ]);
            throw $e;
        }
    }
}
