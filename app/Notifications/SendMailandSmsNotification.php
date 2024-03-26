<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class SendMailandSmsNotification extends Notification implements  ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $mailData;
    protected $filePath;
    protected $fileName;
    protected $trafficId;

    public function __construct($mailData,$trafficId)
    {
        $this->mailData = $mailData;
        $this->trafficId=$trafficId;
        $this->filePath= public_path('storage/uploads/results/'.$this->trafficId.'-result.pdf');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage())
            ->subject('Theory Practice Result')
            ->line($this->mailData['email'])
            ->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'application/pdf', // Adjust the MIME type as needed
            ])
            ->line('For more info visit www.bdc.ae or contact us on 8002354272')
            ->replyTo(['Customer Care' => 'customercare@bdc.ae']);
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
