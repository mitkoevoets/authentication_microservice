<?php

namespace App\Repositories;

use App\Entities\Token;
use App\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface UserRepository
 * @package namespace App\Repositories;
 */
abstract class TokenRepository extends BaseRepository
{

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $token
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function refreshToken($token)
    {
        return $this->update(['token' => $this->generateNewToken(), 'status' => 'available'], $token->id);
    }

    /**
     * @param User $user
     * @param null $targetUrl
     * @return Model|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createOrRefresh(User $user, $targetUrl=null)
    {
        if($user->resetPasswordToken !== null)
        {
            return $this->refreshToken($user->resetPasswordToken);
        }

        return $this->generateAndCreate($user, $targetUrl);
    }

    /**
     * @param User $user
     * @param null $targetUrl
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function generateAndCreate(User $user, $targetUrl=null)
    {
        $data['user_id'] = $user->id;
        $data['token'] = $this->generateNewToken();
        $data['target_url'] = $targetUrl;

        return $this->create($data);
    }

    /**
     * Generates a new token
     *
     * @return string
     */
    private static function generateNewToken()
    {
        return base64_encode(str_random(100));
    }
}
