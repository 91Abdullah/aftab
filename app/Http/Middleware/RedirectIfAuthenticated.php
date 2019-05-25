<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //return redirect('/home');
            switch (Auth::user()->roles()->first()->name) {
                case 'agent':
                    return redirect()->route('agent.index');
                    break;
                case 'admin':
                    return redirect()->route('admin.index');
                    break;
                case 'reporter':
                    return redirect()->route('report.index');
                    break;
                default:
                    return redirect('/');
                    break;
            }
        }

        return $next($request);
    }
}
