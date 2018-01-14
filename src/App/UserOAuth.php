<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use OWolf\Laravel\UserOAuth as BaseUserOAuth;

class UserOAuth extends BaseUserOAuth
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Authenticatable::class, 'user_id', 'id');
    }
}
