<?php

namespace Tests\Feature\User;

use App\Notifications\ResetPasswordNotification;
use App\Entities\User;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordTest extends UserFeatureTest
{
    /** @test */
    public function sends_password_reset_token_to_user()
    {
        $data = $this->registerUser();

        $response = $this->json('POST', $this->forgotPasswordRoute, $data);

        $response->assertStatus(200);
    }

    /** @test */
    public function responds_with_error_if_email_is_missing()
    {
        $this->emailRequired($this->forgotPasswordRoute, 'POST');
    }

    /** @test */
    public function responds_with_error_if_email_is_invalid()
    {
        $this->emailValid($this->forgotPasswordRoute, 'POST');
    }

    /** @test */
    public function responds_with_error_if_email_is_unknown()
    {
        $this->emailExists($this->forgotPasswordRoute, 'POST');
    }
}
