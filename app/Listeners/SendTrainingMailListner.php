<?php

namespace App\Listeners;

use App\Events\SendTrainingMailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use App\Models\Employee;

class SendTrainingMailListner
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
     * @param  SendTrainingMailEvent  $event
     * @return void
     */
    public function handle(SendTrainingMailEvent $event)
    {
        $user=Employee::find($event->userId);
        $details = [
            'title' => 'Alpha Trainings',
            'body' => 'Trainings',
           
        ];

        Mail::to($user->email)->send(new \App\Mail\SendTrainingMail($details));
    }
}
