<?php

namespace App\Supports\Shared;

use App\Entity\Item;

trait HasItemRelation
{
    /**
     * @return mixed
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
