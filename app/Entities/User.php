<?php

namespace App\Entities;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Entities
 */
class User extends Authenticatable implements JWTSubject {

    use HasApiTokens, Notifiable;

    /**
     * @var bool
     */
    public $transactional = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'membership_id',
        'role',
        'status',
        'username_forum',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return mixed
     */
    public function activationToken()
    {
        return $this->hasOne('App\Entities\ActivationToken');
    }

    /**
     * @return mixed
     */
    public function resetPasswordToken()
    {
        return $this->hasOne('App\Entities\ResetPasswordToken');
    }

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
        return [
            'email' => $this->email,
            'role' => $this->role,
            'membership_id' => $this->membership_id,
            'username_forum' => $this->username_forum
        ];
    }

    /**
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Boolean user is active
     *
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }
}
