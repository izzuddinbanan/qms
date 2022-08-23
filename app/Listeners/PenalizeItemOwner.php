<?php

namespace App\Listeners;

use App\Events\ItemCancelled;

class PenalizeItemOwner
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
     * @param  ItemCancelled  $event
     * @return void
     */
    public function handle(ItemCancelled $event)
    {
        if ($event->item->status_id == 14) {
            $event->item->owner->transaction()->create([
                'point'       => -50,
                'description' => 'Penalty from cancelled item',
                'type'        => 'user',
                'item_id'     => $event->item->id,
                'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
                'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            ]);
        }
    }
}
