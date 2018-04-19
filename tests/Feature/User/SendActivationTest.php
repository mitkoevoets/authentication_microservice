<?php

namespace Tests\Feature\User;

use App\Entities\User;
use App\Notifications\ActivationNotifcation;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendActivationTest
 * @package Tests\Feature\User
 */
class SendActivationTest extends UserFeatureTest
{

    /** @test */
    public function sends_activation_notification()
    {
        Notification::fake();

        $data = $this->registerUser(false);

        $user = User::where('email', $data['email'])->first();

        /**
         * Set body and post to api route
         */
        $body = [
            'email' => $user->email
        ];

        $response = $this->json('POST', $this->sendActivationRoute, $body);

        $response->assertStatus(200);
    }

    /** @test */
    public function responds_with_error_if_email_is_missing()
    {
        $this->emailRequired($this->sendActivationRoute, 'POST');
    }

    /** @test */
    public function responds_with_error_if_email_is_invalid()
    {
        $this->emailValid($this->sendActivationRoute, 'POST');
    }
}
