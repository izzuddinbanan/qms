<?php

namespace App\Listeners;

use App\Events\UserRegistered;

class GiveFirstTimeReward
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
     * @param  object  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        \App\Processors\RewardNewUserProcessor::make($event->user)->execute();
    }
}
