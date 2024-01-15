<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendEmailToAllEmployee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $empId;

    public function __construct($empId)
    {
        $this->empId = $empId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $emp = Employee::where('status', 1)->get();
        foreach ($emp as $emp) {
        $details = [
            'title' => 'Employee Notification',
            'name' => $emp->name
        ];

            Mail::to($emp->email)->send(new \App\Mail\SendEmailToAllEmployeeMail($details));
        }
    }
}
