<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class MessageSent extends Notification
{
    use Queueable;
    private $data=[];
    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data=$data;
    }

    public function send(){

    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return [OneSignalChannel::class];
    }

    /**
     * @return OneSignalMessage
     */
    public function toOneSignal(){
        $messageData = $this->data['messageData'];
        return OneSignalMessage::create()
            ->setSubject($messageData['senderName']."sent you a message.")
            ->setBody($messageData['message'])
            ->setData('data',$messageData);
    }


}
