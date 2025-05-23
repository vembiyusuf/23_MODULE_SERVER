<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ChechAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'Unauthenticated',
                'message' => 'Missing token'
            ], 401);
        }

        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'status' => 'Unauthenticated',
                'message' => 'Invalid token'
            ], 401);
        }
        return $next($request);
    }
}
