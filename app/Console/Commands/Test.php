<?php

namespace App\Console\Commands;

use App\Mail\NotifyMail;
use App\Mail\SendMail;
use App\Notifications\SingleUserNotify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        for ($i = 0; $i < 2; $i++) {
            Mail::to('zaeemasif1123@gmail.com')->send(new NotifyMail($i));
        }
    }
}
