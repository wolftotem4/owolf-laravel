<?php

namespace App;

use OWolf\Laravel\UserOAuth as BaseUserOAuth;

class UserOAuth extends BaseUserOAuth
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\\User', 'user_id', 'id');
    }
}
