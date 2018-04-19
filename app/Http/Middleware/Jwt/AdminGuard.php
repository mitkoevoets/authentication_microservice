<?php

namespace IotTmobile\Auth\Http\Middleware\Jwt;

use IotTmobile\Auth\JwtUser;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminGuard
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
        /**
         * @var JwtUser $user
         */
        $user = Auth::user();

        if($user->role === 'admin'){
            return $next($request);
        }

        abort(403, 'Forbidden');
    }
}
