<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use App\Models\User;
use Illuminate\Http\Request;

class AccountVerifyListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public $name;
    public $email;
    public function __construct(Request $request)
    {
        $this->name=$request->name;
        $this->email=$request->email;
    }



    public function handle($event)
    {

        $res=User::where('email',$this->email)->first();
        $details = [
              'id' => $res->id,
            'name' => $this->name,
            'email' => $this->email,

        ];

       $res= Mail::to($this->email)->send(new \App\Mail\AccountVerifyEmail($details));

    }
}
