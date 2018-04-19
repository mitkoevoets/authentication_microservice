<?php

namespace App\Repositories;

use App\Entities\User;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\ResetPasswordToken;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ResetPasswordTokenRepositoryEloquent extends TokenRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ResetPasswordToken::class;
    }
}
