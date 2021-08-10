<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RouterAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $router_session = session()->get('router_session');

        if ($router_session == null) {
            return redirect(route('router_login'));
        } else {
            return $next($request);
        }
    }
}
