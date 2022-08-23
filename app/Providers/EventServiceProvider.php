<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\UserRegistered::class   => [
            \App\Listeners\SendEmailVerification::class,
            \App\Listeners\GiveFirstTimeReward::class,
        ],
        \App\Events\ItemCancelled::class    => [
            \App\Listeners\PenalizeItemOwner::class,
            \App\Listeners\ConsolateSelectedBooker::class,
        ],
        \App\Events\BookingCancelled::class => [
            \App\Listeners\RefundBooker::class,
            \App\Listeners\PenalizeBooker::class,
            \App\Listeners\ConsolateItemOwner::class,
        ],
        \App\Events\BookingSelected::class  => [

        ],
        \App\Events\ItemCollected::class    => [
            \App\Listeners\PayItemOwner::class,
        ],
    ];

    /**
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\RefundItemBookers::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
