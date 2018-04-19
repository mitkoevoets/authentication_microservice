<?php

namespace IotTmobile\Auth\Http\Middleware\Jwt;

use Closure;

class SetTokenFromCookie
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
        $token = $request->cookie('token');

        if($token === null){
            abort(403, 'Forbidden');
        }

        $request->headers->set('Authorization', 'Bearer ' . $token);

        return $next($request);
    }
}
