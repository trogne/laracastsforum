<?php

namespace App\Listeners;

//use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySubscribers //first-class citizen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    //public function __construct()
    //{
    //    //
    //}

    /**
     * Handle the event.
     *
     * @param  ThreadHasNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        //$event->thread->notifySubscribers($event->reply);

        //$event->reply->thread->subscriptions
        //    ->where('user_id', '!=', $event->reply->user_id)
        //    ->each(function ($user) use ($event) {
        //       $user->notify($event->reply);         
        //    });
        $event->reply->thread->subscriptions
            ->where('user_id', '!=', $event->reply->user_id)
            ->each
            ->notify($event->reply);
    }
}
