<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 2. Check if user has the required role
        // The $role parameter comes from the route: middleware('role:admin')
        if ($request->user()->role !== $role) {
            return response()->json([
                'message' => 'Unauthorized. You do not have the ' . $role . ' role.'
            ], 403);
        }

        return $next($request);
    }
}