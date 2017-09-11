<?php

namespace LaravelVue\Talk\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use LaravelVue\Talk\Facades\Talk;

class TalkMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            Talk::setAuthUserId(Auth::guard($guard)->user()->id);
        }

        return $next($request);
    }
}
