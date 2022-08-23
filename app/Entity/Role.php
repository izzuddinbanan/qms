<?php

namespace App\Entity;

// use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * @return mixed
     */
    public function project()
    {
        return $this->belongsToMany(Project::class, 'role_user');
    }
}
