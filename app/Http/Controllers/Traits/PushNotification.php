<?php

namespace App\Http\Controllers\Traits;

use FCM;
use Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Illuminate\Http\Request;
use App\Entity\UserDevice;
use App\Entity\User;
use App\Entity\Notification;
use App\Entity\Issue;

trait PushNotification
{

    function FCMnotification($title = 'QMS', $message, $appData, $user_id, $issue_id = null, $push_by){
        
        /**
         *brozot/Laravel-FCM
         **/

        $token = UserDevice::whereIn('user_id', $user_id)->pluck('push_token','id')->toArray();

        foreach ($user_id as $value) {


           $notification = Notification::create([
                'user_id'        => $value['user_id'],
                'payload'        => json_encode($appData),
                'message'        => $message,
                'type'           => $appData["type"],
                'issue_id'       => $issue_id,
                'push_by'        => $push_by,
            ]);

           if($issue_status = Issue::find($issue_id)){

                $notification->forcefill(['issue_status' => $issue_status->status_id])->save();
            }

        }

        // if($token = UserDevice::whereIn('user_id', $user_id)->pluck('push_token','id')->toArray()){
            
        //     foreach ($token as $key => $value) {
                
        //         $UserDevice = UserDevice::where('id', $key)->select('user_id')->first();

        //         $notification = Notification::create([
        //             'user_id'        => $UserDevice->user_id,
        //             'user_device_id' => $key,
        //             'payload'        => json_encode($appData),
        //             'message'        => $message,
        //             'type'           => $appData["type"],
        //             'issue_id'       => $issue_id,
        //             'push_by'        => $push_by,
        //         ]);

        //         if($issue_status = Issue::find($issue_id)){

        //             $notification->forcefill(['issue_status' => $issue_status->status_id])->save();
        //         }

        //     }

        // }else{

        //     foreach ($user_id as $value) {

        //         $notification = Notification::create([
        //             'user_id'        => $value["user_id"],
        //             'user_device_id' => null,
        //             'payload'        => json_encode($appData),
        //             'message'        => $message,
        //             'issue_id'       => $issue_id,
        //             'push_by'        => $push_by,
        //         ]);

        //         if($issue_status = Issue::find($issue_id)){

        //             $notification->forcefill(['issue_status' => $issue_status->status_id])->save();
        //         }

        //     }

        // }

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)
            ->setSound('default');

        $appData['show_in_foreground'] = true;
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($appData);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $datatest = $dataBuilder->build();

        if (!empty($token)) {
            $downstreamResponse = FCM::sendTo($token, $option, $notification, $datatest);
        }

    }
    
}
