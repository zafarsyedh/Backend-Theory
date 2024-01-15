<?php

namespace App\Jobs;

use App\Mail\ShahraanTech;
use App\Mail\ShahraanTechMail;
use App\Models\EmailCompagin;
use Google\Service\AdMob\App;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class SendEmailToLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    protected $filename;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details, $filename)
    {
        $this->details = $details;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $res = EmailCompagin::where('status', 0)->get();
        foreach ($res as $row) {
            $email = EmailCompagin::where('email', $row->email)->first();
            $email->status = 1;
            $email->save();
            Mail::to($row->email)->send(new ShahraanTechMail($this->details, $this->filename));
        }
    }
}
