<?php

namespace App\Supports\Shared;

use App\Entity\User;

trait HasUserRelation
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
