<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;

class SendEmailVerification
{
    use VerifiesUsers;
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
        UserVerification::generate($event->user);
        UserVerification::send($event->user, 'Baby Block Email Verification');
    }
}
