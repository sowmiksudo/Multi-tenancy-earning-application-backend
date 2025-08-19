<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->otp_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not verified'
            ], 403);
        }
        return $next($request);
    }
}
