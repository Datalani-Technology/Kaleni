<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin panel.');
        }
        
        $role = auth()->user()->role;
        if (!in_array($role, ['admin', 'editor'], true)) {
            auth()->logout();
            return redirect()->route('admin.login')->with('error', 'You do not have access to the admin panel.');
        }
        
        return $next($request);
    }
}
