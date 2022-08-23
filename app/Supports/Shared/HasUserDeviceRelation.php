<?php

namespace App\Supports\Shared;

use App\Entity\UserDevice;

trait HasUserDeviceRelation
{
    /**
     * @return mixed
     */
    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id', 'id');
    }
}
