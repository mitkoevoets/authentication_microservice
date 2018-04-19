<?php

namespace App\Entities;

/**
 * Class ActivationToken
 * @package App\Entities
 */
class ActivationToken extends Token
{
    /**
     * @var bool
     */
    public $transactional = true;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'status',
        'target_url'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }
}
