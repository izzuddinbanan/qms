<?php

namespace App\Supports\Shared;

use App\Entity\Module;

trait HasModuleRelation
{
    /**
     * @return mixed
     */
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }
}
