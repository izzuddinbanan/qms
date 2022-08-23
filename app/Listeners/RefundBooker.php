<?php

namespace App\Listeners;

use App\Events\BookingCancelled;

class RefundBooker
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
        $event->user->transaction()->create([

            'point'       => $event->booking->item->point,
            'description' => $event->booking->item->name,
            'type'        => 'item',
            'item_id'     => $event->booking->item->id,
            'status'      => 'Refunded',
            'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),

        ]);
    }
}
