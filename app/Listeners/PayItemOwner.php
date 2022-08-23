<?php

namespace App\Listeners;

use App\Events\ItemCollected;

class PayItemOwner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ItemCollected  $event
     * @return void
     */
    public function handle(ItemCollected $event)
    {
        $event->item->owner->transaction()->create([
            'point'       => $event->item->point,
            'description' => $event->item->name,
            'type'        => 'item',
            'item_id'     => $event->item->id,
            'status'      => 'Collected',
            'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
        ]);
    }
}
