<?php

namespace Tests\Feature\User;

use App\Notifications\ActivationNotifcation;
use App\Entities\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Class RegisterTest
 * @package Tests\Feature\User
 */
class RegisterTest extends UserFeatureTest
{

    /** @test */
    public function registers_user()
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

        $response = $this->json('POST', $this->registerRoute, $body);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    /** @test */
    public function responds_with_error_if_required_data_is_incomplete()
    {
        $body = [
            'lorem' => 'ipsum',
        ];

        $response = $this->json('POST', $this->registerRoute, $body);

        $response->assertStatus(422);

    }

    /** @test */
    public function responds_with_error_if_password_is_not_valid()
    {
        $body = [
            'email' => 'test@testing.test',
            'membership_id' => '9999999902020',
            'password' => '12'
        ];

        $response = $this->json('POST', $this->registerRoute, $body);

        $response->assertStatus(422);
    }

    /** @test */
    public function responds_with_error_if_email_is_not_unique()
    {
        $data = $this->registerUser();

        /**
         * Set body and post to api route
         */
        $body = [
            'email' => $data['email'],
            'membership_id' => '9999999902020',
            'password' => '123456',
        ];

        $response = $this->json('POST', $this->registerRoute, $body);

        $response->assertStatus(422);
    }
}
