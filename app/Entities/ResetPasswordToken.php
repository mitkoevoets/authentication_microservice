<?php

namespace App\Entities;

/**
 * Class ResetPasswordToken
 * @package App\Entities
 */
class ResetPasswordToken extends Token
{
    /**
     * @var bool
     */
    public $transactional = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }

    /**
     * @param $newPassword
     * @return bool
     */
    public function resetPassword($newPassword)
    {
        $this->user->resetPassword($newPassword);

        $this->status = 'used';

        return $this->save();
    }
}
