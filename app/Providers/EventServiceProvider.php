<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
//use Illuminate\Auth\Events\Registered;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //'App\Events\Event' => [
        //    'App\Listeners\EventListener',
        //],
        //'App\Events\ThreadHasNewReply' => [
        //    'App\Listeners\NotifyThreadSubscribers',
        //],
        'App\Events\ThreadReceivedNewReply' => [
            'App\Listeners\NotifyMentionedUsers',
            'App\Listeners\NotifySubscribers',
        ],
        
        ////NO MORE USING Registered EVENT/LISTENER
        //Registered::class => [
        //    'App\Listeners\SendEmailConfirmationRequest',
        //]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
