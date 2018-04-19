<?php

namespace Tests\Feature\Member;

use App\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Var\Auth\Testing\MocksUsers;
use Var\SendsResponses\Testing\BaseRestTest;

class RestTest extends BaseRestTest
{
    use MocksUsers;

    /**
     * @var string
     */
    protected $restRoute = '/api/user';

    /**
     * @return mixed
     */
    protected function setUpEntity()
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
            'status' => 'active',
        ];

        $response = $this->json('POST', $this->registerRoute, $body);
        $response->assertStatus(201);

        $entity = User::where('email', $email)->first();

        return $entity;
    }

    /**
     * @return mixed
     */
    protected function setUpEntities()
    {
        Notification::fake();

        $entities = [];

        $bodies = [
            [
                'email' => 'test1@test.test',
                'membership_id' => '9999999902020',
                'password' => '123456',
                'username_forum' => '123456',
                'status' => 'active',
            ],
            [
                'email' => 'test2@test.test',
                'membership_id' => '9999999902020',
                'password' => '123456',
                'username_forum' => '1234567',
                'status' => 'active',
            ]
        ];

        foreach($bodies as $body){

            $this->json('POST', $this->registerRoute, $body);

            $entities[] = User::where('email', $body['email'])->first();
        }

        return $entities;
    }

    /**
     * @return mixed
     */
    protected function singleTerminology()
    {
        return 'user';
    }

    /**
     * @return mixed
     */
    protected function pluralTerminology()
    {
        return 'users';
    }

    /**
     * @param Model $entity
     * @param boolean $updateBody
     * @return array
     */
    protected function updateTestBody(Model $entity, $updateBody)
    {
        /**
         * @var User $user
         *
         */
        $user = $entity;

        return [
            'email' => ($updateBody ? 'updated-' : '') . $user->email,
        ];
    }
}
