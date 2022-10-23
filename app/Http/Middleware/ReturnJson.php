<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReturnJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // explicitly setting 'Accept: application/json' forces json response
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
