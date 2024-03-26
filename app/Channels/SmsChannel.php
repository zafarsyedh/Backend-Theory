<?php

namespace App\Channels;


use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = method_exists($notification, 'toSms')
            ? $notification->toSms($notifiable)
            : [];
        if (empty($data)) {
            return;
        }
        return true;
    }
}
