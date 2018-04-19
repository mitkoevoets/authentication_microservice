<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EmailCriteria
 * @property  $email
 * @package namespace App\Criteria;
 */
class EmailCriteria implements CriteriaInterface
{

    /**
     * @var string
     */
    private $email;

    /**
     * EmailCriteria constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
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
        $model = $model->where('email', '=', $this->email);
        return $model;
    }
}
