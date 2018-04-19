<?php

namespace Tests\Feature\User;

use App\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends UserFeatureTest
{

    /** @test */
    public function logs_in_user()
    {
        $data = $this->registerUser();

        $response = $this->json('POST', $this->loginRoute, $data);

        $response
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ])
            ->assertStatus(200);
    }

    /** @test */
    public function responds_with_error_if_required_data_is_incomplete()
    {
        $this->registerUser();

        $body = [
            'lorem' => 'ipsum',
        ];

        $response = $this->json('POST', $this->loginRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email.required'
                    ],
                    'password' => [
                        'password.required'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_email_is_unknown()
    {
        $data = $this->registerUser();

        $body = [
            'email' => 'anotheremail@testing.test',
            'password' => $data['password']
        ];

        $response = $this->json('POST', $this->loginRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'user.credentials'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_password_is_not_valid()
    {
        $data = $this->registerUser();

        $body = [
            'email' => $data['email'],
            'password' => '12'
        ];

        $response = $this->json('POST', $this->loginRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'email' => [
                        'user.credentials'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_user_status_is_not_active()
    {
        $data = $this->registerUser(false);

        $body = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        $response = $this->json('POST', $this->loginRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'global' => [
                        'user.status'
                    ]
                ]
            ])
            ->assertStatus(422);
    }
}
