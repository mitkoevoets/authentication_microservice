<?php

namespace Tests\Feature\User;


use App\Entities\User;

class ImportTest extends UserFeatureTest
{
    /**
     * @test
     */
    public function creates_imported_user()
    {
        $data = [
            'membership_id' => 'MIG1234567890123456',
            'email' => 'test@example.com',
            'password' => 'portable-bcrypt-hash-do-not-touch',
            'username_forum' => 'fuckingdickhead88',
        ];

        $response = $this->json('POST', $this->importUserRoute, $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'membership_id' => 'MIG1234567890123456',
            'email' => 'test@example.com',
            'password' => 'portable-bcrypt-hash-do-not-touch',
            'status' => 'active',
            'username_forum' => 'fuckingdickhead88',
        ]);
    }

    /**
     * @test
     */
    public function responds_with_error_if_user_exists()
    {
        User::create([
            'membership_id' => 'foo',
            'email' => 'test@example.com',
            'password' => 'bar',
            'username_forum' => 'fuckingdickhead88',
        ]);

        $data = [
            'membership_id' => 'MIG1234567890123456',
            'email' => 'test@example.com',
            'password' => 'portable-bcrypt-hash-do-not-touch',
            'username_forum' => 'fuckingdickhead88',
        ];

        $response = $this->json('POST', $this->importUserRoute, $data);

        $response->assertStatus(422);
    }
}