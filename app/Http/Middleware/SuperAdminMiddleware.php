<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Super Admin privileges required.'], 403);
            }

            return redirect()->route('login')->with('error', 'Access denied. Super Admin privileges required.');
        }

        $response = $next($request);

        // Prevent browser from caching authenticated pages.
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
