<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @return \Dingo\Api\Http\Response
     */
    public function refreshToken(){
        /**
         * @var JWTAuth $jwtAuth
         */
        $jwtAuth = Auth::guard('api');

        $token = $jwtAuth->getToken();

        try{
            /**
             *
             */
            $token = $jwtAuth->refresh($token);

            return $this->successResponse('Token refreshed', 200, ['token' => $token]);
        } catch(TokenInvalidException $e){
            abort(403, 'Forbidden');
        }

        abort(500);
    }
}