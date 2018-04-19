<?php

namespace Tests\Feature\User;

use App\Entities\ResetPasswordToken;
use App\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends UserFeatureTest
{
    /** @test */
    public function resets_user_password()
    {
        $data = $this->registerUser();

        $this->json('POST', $this->forgotPasswordRoute, $data);

        $user = User::where('email', $data['email'])->first();

        $resetBody = [
            'token' => $user->resetPasswordToken->getToken(),
            'password'  => '1234567'
        ];

        $response = $this->json('POST', $this->resetPasswordRoute, $resetBody);

        $response->assertStatus(200);

        $loginBody = [
            'email' => $user->email,
            'password'  => '1234567'
        ];

        $response = $this->json('POST', $this->loginRoute, $loginBody);

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
        $body = [
            'lorem' => 'ipsum',
        ];

        $response = $this->json('POST', $this->resetPasswordRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'token' => [
                        'token.required'
                    ],
                    'password' => [
                        'password.required'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_given_password_does_not_meet_requirements()
    {
        $body = [
            'token' => 'test',
            'password' => 'test'
        ];

        $response = $this->json('POST', $this->resetPasswordRoute, $body);

        $response
            ->assertJson([
                'errors' => [
                    'password' => [
                        'password.min'
                    ]
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_token_is_not_valid()
    {
        $body = [
            'token' => 'test',
            'password' => '1234567'
        ];

        $response = $this->json('POST', $this->resetPasswordRoute, $body);

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
    public function responds_with_error_if_token_is_not_available()
    {
        $resetPasswordToken = ResetPasswordToken::create([
            'user_id' => '1',
            'status' => 'used',
            'token' => base64_encode('test')
        ]);

        $token = $resetPasswordToken->getToken();

        $body = [
            'token' => $token,
            'password' => '1234567'
        ];

        $response = $this->json('POST', $this->resetPasswordRoute, $body);

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
