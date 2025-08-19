<?php

namespace App\Http\Middleware;

use App\Models\UserIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLastIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $last_ip = UserIp::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first()->ip_address;
        $this->ip_address = $request->ip();
        return $next($request);
    }
}
