<?php
namespace Tests\Feature\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Var\Auth\Http\Middleware\Jwt\AdminGuard;
use Var\Auth\Http\Middleware\Jwt\Guard;
use Var\Auth\Http\Middleware\Jwt\MembershipGuard;
use Var\Auth\JwtUser;
use Tests\TestCase;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

abstract class UserFeatureTest extends TestCase
{

    /**
     * Sets up the test for login by registering a user.
     * Returns the data which was used to register the user
     *
     * @param bool $activate
     * @return array
     */
    protected function registerUser($activate = true, $targetUrl=null)
    {
        Notification::fake();

        /**
         * Set body and post to api route
         */
        $email = 'test@testing.test';

        $body = [
            'email' => $email,
            'membership_id' => '9999999902020',
            'password' => '123456',
            'username_forum' => '123456',
        ];

        if(!is_null($targetUrl)) {
            $body['target_url'] = $targetUrl;
        }

        $response = $this->json('POST', $this->registerRoute, $body);
        $response->assertStatus(201);

        if($activate)
        {
            DB::table('users')
                ->where('email', $email)
                ->update(['status' => 'active']);
        }

        return $body;
    }

    /**
     * @param string $role
     */
    protected function mockJwtAuth($role = 'admin')
    {
        $this->withoutMiddleware([Guard::class, Authenticate::class, AdminGuard::class, MembershipGuard::class]);

        /**
         * TODO: add testing with admin role (currently guard is returning null on Auth::user() in middleware)
         */

        /**
         * Mock User
         */
        $user = new JwtUser([
            'id' => '1',
            'sub' => '1',
//                'email' => 'lorem@ipsum.test',
            'username' => 'test',
            'role' => $role
        ]);

        Auth::guard('jwtauth')->setUser($user);
    }

    /**
     * Some generic email tests
     */
    protected function emailRequired($route, $method = 'GET')
    {
        $body = [
            'lorem' => 'ipsum',
        ];

        $response = $this->json($method, $route, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email.required'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    protected function emailValid($route, $method = 'GET')
    {
        $body = [
            'email' => 'ipsum.',
        ];

        $response = $this->json($method, $route, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email.email'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    protected function emailExists($route, $method = 'GET')
    {

        $body = [
            'email' => 'ipsum@ipsum.com',
        ];

        $response = $this->json($method, $route, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email.exists'
                    ]
                ]
            ])
            ->assertStatus(422);
    }
}
