<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'idempotency:' . md5($request->getContent());
        if(Cache::has($key)) {
            // 409: Conflict - Duplicate request
            return response()->json(['message' => 'Duplicate request'], 409);
        }
        Cache::put($key, true, now()->addSeconds(10)); // Store key for 10 seconds

        return $next($request);
    }
}
