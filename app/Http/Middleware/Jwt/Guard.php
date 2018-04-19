<?php

namespace IotTmobile\Auth\Http\Middleware\Jwt;

use IotTmobile\Auth\JwtUser;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class Guard
 * @package IotTmobile\Auth\Http\Middleware\Jwt
 */
class Guard
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

        if($user === null){
            abort(403, 'Forbidden');
        }

        $user->setAttributes(Auth::guard()->parseToken()->getPayload());

        return $next($request);
    }
}
