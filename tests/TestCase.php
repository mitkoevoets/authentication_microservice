<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    /**
     * @var string
     */
    protected $registerRoute = '/api/register';

    /**
     * @var string
     */
    protected $activateRoute = '/api/activate';

    /**
     * @var string
     */
    protected $loginRoute = '/api/login';

    /**
     * @var string
     */
    protected $findUserRoute = '/api/user/find';

    /**
     * @var string
     */
    protected $allUsersRoute = '/api/user/all';

    /**
     * @var string
     */
    protected $updateUserRoute = '/api/user/update';

    /**
     * @var string
     */
    protected $changePasswordRoute = '/api/change-password';

    /**
     * @var string
     */
    protected $forgotPasswordRoute = '/api/password/forgot';

    /**
     * @var string
     */
    protected $resetPasswordRoute = '/api/password/reset';

    /**
     * @var string
     */
    protected $sendActivationRoute = '/api/send-activation';

    /**
     * @var string
     */
    protected $validateEmailRoute = '/api/validate-email';

    /**
     * @var string
     */
    protected $importUserRoute = 'api/user/import';

}
