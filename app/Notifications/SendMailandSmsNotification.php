<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class SendMailandSmsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $mailData;
    protected $filePath;
    protected $fileName;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
        $this->filePath= public_path('storage/uploads/results/16109070-result.pdf');
        $this->fileName='16109070.pdf';

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
            ->subject('Exam Result Email')
            ->greeting('Dear Salman Raza,')
            ->line('BDC Student Portal verification code is mentioned below. It will expire after 10 minutes.')
            ->line(new HtmlString(' <div style="letter-spacing: 1rem;  text-align: center; width: 200px; padding: 10px 20px; padding-right: 0px; border-radius: 0.5rem; margin: 0 auto; font-weight: 700; font-size: 24px; background-color: gray; color: white">16109070</div>'))

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
