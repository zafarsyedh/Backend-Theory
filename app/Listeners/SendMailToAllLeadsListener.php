<?php

namespace App\Listeners;

use App\Events\SendMailToAllLeadsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class SendMailToAllLeadsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public $subject;
    public $textBody;
    public function __construct(Request $request)
    {
        dd($request);
        $this->name=$request->subject;
        $this->textBody=$request->text_body;

    }

    /**
     * Handle the event.
     *
     * @param  SendMailToAllLeadsEvent  $event
     * @return void
     */
    public function handle($event)
    {
        dd($event);
    }
}
