<?php

namespace Tests\Feature\User;

use App\Entities\ActivationToken;
use App\Entities\User;

/**
 * Class ActivationTest
 * @package Tests\Feature\User
 */
class ActivationTest extends UserFeatureTest
{

    /** @test */
    public function activates_user()
    {
        $data = $this->registerUser(false);

        $user = User::where('email', $data['email'])->first();

        /**
         * Set body and post to api route
         */
        $body = [
            'token' => $user->activationToken->getToken()
        ];

        $response = $this->json('POST', $this->activateRoute, $body);

        $response
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ])
            ->assertStatus(200);

        $user = User::where('email', $data['email'])->first();

        $this->assertTrue($user->status === 'active');

        $this->assertTrue($user->activationToken->status === 'used');
    }

    /** @test */
    public function returns_activation_target_url()
    {
        $data = $this->registerUser(false, 'http://var.nl');

        $user = User::where('email', $data['email'])->first();

        /**
         * Set body and post to api route
         */
        $body = [
            'token' => $user->activationToken->getToken()
        ];

        $response = $this->json('POST', $this->activateRoute, $body);
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'target_url' => 'http://var.nl'
            ]
        ]);
    }

    /** @test */
    public function responds_with_error_if_token_is_missing()
    {
        $body = [
            'lorem' => 'ipsum',
        ];

        $response = $this->json('POST', $this->activateRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'token' => [
                        'token.required'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_token_is_unknown()
    {
        $body = [
            'token' => 'test',
        ];

        $response = $this->json('POST', $this->activateRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'token' => [
                        'token.status'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_token_status_is_invalid()
    {
        $data = $this->registerUser(false);

        $user = User::where('email', $data['email'])->first();

        /**
         * @var ActivationToken $activationToken
         */
        $activationToken = $user->activationToken;

        $activationToken->status = 'used';
        $activationToken->save();

        $body = [
            'token' => $activationToken->getToken(),
        ];

        $response = $this->json('POST', $this->activateRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'token' => [
                        'token.status'
                    ]
                ]
            ])
            ->assertStatus(422);
    }
}
