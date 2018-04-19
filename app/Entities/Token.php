<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class Token
 * @package App\Entities
 */
abstract class Token extends Model implements JWTSubject
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'status'
    ];


    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Gets token (decoded)
     *
     * @return bool|string
     */
    public function getToken()
    {
        return base64_decode($this->token);
    }
}
