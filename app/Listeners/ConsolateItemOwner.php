<?php

namespace App\Listeners;

use App\Events\BookingCancelled;

class ConsolateItemOwner
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
     * @param  BookingCancelled  $event
     * @return void
     */
    public function handle(BookingCancelled $event)
    {
        if ($event->booking->status_id == 10) {

            $event->booking->item->owner->transaction()->create([
                'point'       => 50,
                'description' => 'Consolation from cancelled booking',
                'type'        => 'user',
                'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
                'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            ]);
        }
    }
}
