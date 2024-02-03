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

    public function pushedData(Request  $request){
         $request->all();
        $data=[
            'stdName'=>$request->std_name,
            'qLang'=>$request->lang,
            'ingName'=>$request->invg_name,
            'courseName'=>$request->course

        ];
        event(new CourseEvent($data));
        dd('your data pushed succeefully');
    }

    public function examShow(){
        return view('exam');
    }
}
