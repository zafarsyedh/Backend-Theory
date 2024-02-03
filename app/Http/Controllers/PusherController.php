<?php

namespace App\Http\Controllers;

use App\Events\CourseEvent;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index($text){
        event(new MessageSent($text));
    }

    public function pushedData(){
        $data=[
            'stdName'=>'Salman Raza',
            'qLang'=>'Eng',
            'ingName'=>'Faheem',
            'courseName'=>'LMV'

        ];
        event(new CourseEvent($data));
    }

    public function examShow(){
        return view('exam');
    }
}
