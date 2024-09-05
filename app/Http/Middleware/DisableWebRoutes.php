<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableWebRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add condition to disable web routes, e.g., a config setting
        if (config('app.disable_web_routes')) {
            abort(403, 'Web routes are disabled.');
        }

        return $next($request);
    }
}