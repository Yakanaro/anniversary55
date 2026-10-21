<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHostIsAuthenticated
{
    /**
     * Only let the host, who has entered the guest list password, through.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('host_authenticated', false)) {
            return redirect()->route('host.login');
        }

        return $next($request);
    }
}
