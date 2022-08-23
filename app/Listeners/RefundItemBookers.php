<?php

namespace App\Listeners;

use App\Events\ItemCancelled;
use App\Events\BookingSelected;

class RefundItemBookers
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
    public function onItemCancelled(ItemCancelled $event)
    {
        if ($event->item->status_id == 13) {

            if ($event->item->bookings()->exists()) {

                $event->item->bookings->each(function ($booking) use ($event) {
                    $this->newTransaction($booking, $event->item, 'Refunded');
                });
            }
        } elseif ($event->item->status_id == 14) {

            $booking = $event->item->bookings()->where('status_id', 10)->first();
            $this->newTransaction($booking, $event->item, 'Refunded');
        }
    }

    /**
     * Handle the event.
     *
     * @param  BookingSelected  $event
     * @return void
     */
    public function onBookingSelected(BookingSelected $event)
    {
        if ($otherBookings = $event->item->bookings()->whereNotIn('status_id', [10])->get()) {
            $otherBookings->each(function ($booking) use ($event) {

                $booking->update(['status_id' => 8]);
                $this->newTransaction($booking, $event->item, 'Taken');
            });
        }
    }

    /**
     * @param $booking
     * @param $status
     */
    public function newTransaction($booking, $item, $status = '-')
    {
        $booking->receiver->transaction()->create([
            'point'       => $item->point,
            'description' => $item->name,
            'type'        => 'item',
            'item_id'     => $item->id,
            'status'      => $status,
            'created_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            'updated_at'  => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
        ]);
    }

    /**
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\ItemCancelled::class,
            'App\Listeners\RefundItemBookers@onItemCancelled'
        );

        $events->listen(
            \App\Events\BookingSelected::class,
            'App\Listeners\RefundItemBookers@onBookingSelected'
        );
    }
}
