<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Trainer;
use Illuminate\Http\Request;

class SaveTrainerInUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    
    public $name;
    public $email;
    public $password;
    public function __construct(Request $request)
    {
        $this->name=$request->f_name;
        $this->email=$request->email;
        $this->password=$request->password;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $res=Trainer::where('email',$this->email)->first();
         return User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'trainer_id' => $res->id,
            'role' => 'trainer',
        ]);
    }
}
