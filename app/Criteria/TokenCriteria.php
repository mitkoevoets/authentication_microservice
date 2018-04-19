<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EmailCriteria
 * @property  token
 * @package namespace App\Criteria;
 */
class TokenCriteria implements CriteriaInterface
{

    /**
     * @var string
     */
    private $token;

    /**
     * EmailCriteria constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $encodedToken = base64_encode($this->token);

        $model = $model->where('token', '=', $encodedToken)->orderBy('created_at', 'desc');
        return $model;
    }
}
