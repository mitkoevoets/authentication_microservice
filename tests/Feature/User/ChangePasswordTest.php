<?php

namespace Tests\Feature\User;

use App\Entities\User;

/**
 * Class ChangePasswordTest
 * @package Tests\Feature\User
 */
class ChangePasswordTest extends UserFeatureTest
{

    /** @test */
    public function changes_password_of_user()
    {
        $this->mockJwtAuth();

        $data = $this->registerUser(true);

        /**
         * @var User $user;
         */
        $user = User::where('email', $data['email'])->first();

        $newPassword = '1234567';

        /**
         * Set body and post to api route
         */
        $body = [
            'user_id'   => $user->id,
            'password_old' => $data['password'],
            'password_new' => $newPassword
        ];

        $response = $this->json('POST', $this->changePasswordRoute, $body);

        $response->assertStatus(200);

        /**
         * Attempt to login with new password.
         */
        $data['password'] = $newPassword;

        $response = $this->json('POST', $this->loginRoute, $data);

        $response->assertStatus(200);
    }

    /** @test */
    public function returns_error_if_old_password_incorrect()
    {
        $this->mockJwtAuth();

        $data = $this->registerUser(true);

        /**
         * @var User $user;
         */
        $user = User::where('email', $data['email'])->first();

        $newPassword = '1234567';

        /**
         * Set body and post to api route
         */
        $body = [
            'user_id'   => $user->id,
            'password_old' => 'foobar123',
            'password_new' => $newPassword
        ];

        $response = $this->json('POST', $this->changePasswordRoute, $body);

        $response->assertStatus(422);

        /**
         * Attempt to login with new password.
         */
        $data['password'] = $newPassword;

        $response = $this->json('POST', $this->loginRoute, $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function returns_error_if_user_does_not_exist()
    {
        $this->mockJwtAuth();

        /**
         * Set body and post to api route
         */
        $body = [
            'user_id'   => 1,
            'password_old' => 'foobar1',
            'password_new' => 'foobar2'
        ];

        $response = $this->json('POST', $this->changePasswordRoute, $body);

        $response->assertStatus(404);
    }
}
