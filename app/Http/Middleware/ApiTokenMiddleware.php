<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-API-KEY');

        if (!$token || $token !== env('API_TOKEN')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token',
                'server_time' => now()->toDateTimeString(),
            ], 401);
        }

        return $next($request);
    }
}
