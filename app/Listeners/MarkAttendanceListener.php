<?php

namespace App\Listeners;

use App\Models\Attendance;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class MarkAttendanceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */


    public function __construct( )
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {


        $empId=\Auth::user()->account_id;
        if (!($res = Attendance::where([['emp_id', $empId], ['date', date('Y-m-d')]])->first())) {

            $att = new Attendance();
            $att->emp_id = $empId;
            $att->marked_by = 'self';
            $att->date = date('Y-m-d');
            $att->attendance_date = date('d', strtotime(date('Y-m-d')));
            $att->attendance_month = date('m', strtotime(date('Y-m-d')));
            $att->attendance_year = date('Y', strtotime(date('Y-m-d')));
            $att->save();


        }


    }
}
