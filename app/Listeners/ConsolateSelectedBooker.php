<?php

namespace App\Listeners;

use App\Events\ItemCancelled;

class ConsolateSelectedBooker
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

            $booking = $event->item->bookings()->where('status_id', 10)->first();

            $booking->receiver->transaction()->create([
                'point'       => 50,
                'description' => 'Consolation from cancelled item',
                'type'        => 'user',
                'item_id'     => $event->item->id,
                'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
                'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),

            ]);

        }
    }
}
