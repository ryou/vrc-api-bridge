<?php

namespace App\Http\Middleware;

use Closure;

class ResponseHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
                ->header("Access-Control-Allow-Origin", config("app.allow-origin"))
                ->header("Access-Control-Allow-Credentials", "true")
                ->header("Access-Control-Allow-Methods", "POST, GET")
                ->header("Access-Control-Allow-Headers", "Content-Type");
    }
}
