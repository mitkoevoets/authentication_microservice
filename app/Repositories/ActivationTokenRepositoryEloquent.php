<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\ActivationToken;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ActivationTokenRepositoryEloquent extends TokenRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ActivationToken::class;
    }
}
