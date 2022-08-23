<?php

namespace App\Notifications;

use NotificationChannels\FCM\FCMMessage;
use Illuminate\Notifications\Notification;

class PushNotification extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFCM($notifiable)
    {
        $data = $this->data;

        // dd($data);
        return (new FCMMessage())
            ->notification([
                'title' => $data['title'],
                'body'  => $data['message'],
                'sound' => 'default',
            ])
            ->data(["payload" => $data['payload']]);
    }

}
