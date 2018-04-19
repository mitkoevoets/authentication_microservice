<?php

namespace Tests\Feature\User;

use App\Entities\User;

/**
 * Class ValidateEmailTest
 * @package Tests\Feature\User
 */
class ValidateEmailTest extends UserFeatureTest
{

    /** @test */
    public function responds_with_success_if_user_is_known()
    {
        $data = $this->registerUser(true);

        $user = User::where('email', $data['email'])->first();

        /**
         * Set body and post to api route
         */
        $body = [
            'email' => $user->email
        ];

        $response = $this->json('GET', $this->validateEmailRoute, $body);

        $response
            ->assertJson([
                'data' => [
                    'email' => [
                        'user.found'
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    public function responds_with_success_if_user_status_is_not_active()
    {
        $data = $this->registerUser(false);

        $user = User::where('email', $data['email'])->first();

        /**
         * Set body and post to api route
         */
        $body = [
            'email' => $user->email
        ];

        $response = $this->json('GET', $this->validateEmailRoute, $body);

        $response
            ->assertJson([
                'data' => [
                    'email' => [
                        'user.status'
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    /** @test */
    public function responds_with_success_if_email_is_unknown()
    {
        $body = [
            'email' => 'ipsum@ipsum.com',
        ];

        $response = $this->json('GET', $this->validateEmailRoute, $body);

        $response
            ->assertJson([
                'data' => [
                    'email' => [
                        'available'
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    /** @test */
    public function responds_with_error_if_email_is_missing()
    {
        $this->emailRequired($this->validateEmailRoute);
    }

    /** @test */
    public function responds_with_error_if_email_is_invalid()
    {
        $this->emailValid($this->validateEmailRoute);
    }
}
