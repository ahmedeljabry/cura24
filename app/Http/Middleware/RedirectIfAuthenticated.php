<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
//        \Log::debug('RedirectIfAuthenticated: Guard=' . $guard . ', Path=' . $request->path() . ', Admin authenticated=' . Auth::guard('admin')->check());

        // Allow admin guard users to access /login
        if ($guard === 'admin' && Auth::guard('admin')->check() && ($request->route()->named('user.login') || $request->route()->named('user.register'))) {
            return $next($request);
        }

        // Redirect web guard users based on user_type
        if ($guard === 'web' && Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->user_type == 0) {
                return redirect()->route('seller.dashboard');
            } elseif ($user->user_type == 1) {
                return redirect()->route('buyer.dashboard');
            }
        }

        // Redirect other admin guard users to admin-home
        if ($guard === 'admin' && Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }

        return $next($request);
    }
}