<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'App\Event\InterviewScheduled'=>[
            'App\Listeners\SendEmail',
        ],

         'App\Events\SendTrainingMailEvent'=>[
            'App\Listeners\SendTrainingMailListner',
        ],
          'App\Events\SaveTrainerInUserEvent'=>[
            'App\Listeners\SaveTrainerInUserListener',
        ],
         'App\Events\AccountVerifyEvent'=>[
            'App\Listeners\AccountVerifyListener',
        ],

         'App\Events\MarkAttendanceEvent'=>[
            'App\Listeners\MarkAttendanceListener',
        ],

        'App\Events\TransactionEvent'=>[
            'App\Listeners\TransactionListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
