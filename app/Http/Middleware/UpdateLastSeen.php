<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (Auth::check()) {
  $user = Auth::user();
            $user->update([
                'last_seen_at' => now(),
                'last_login_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

        }
        return $next($request);
    }
}
