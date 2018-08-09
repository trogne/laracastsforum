<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        ////preg_match_all('/\@([^\s\.]+)/', $event->reply->body, $matches);
        //$mentionedUsers = $event->reply->mentionedUsers();

        ////foreach($matches[1] as $name) {
        //foreach($mentionedUsers as $name) {
        //    if($user = User::whereName($name)->first()) {
        //        $user->notify(new YouWereMentioned($event->reply));
        //    }
        //}
        
        //collect($event->reply->mentionedUsers())
        //    ->map(function ($name) {
        //        return User::whereName($name)->first();
        //    })->filter(function ($user) use ($event) {
        //        $user->notify(new YouWereMentioned($event->reply));
        //    }); //fonctionne aussi
        //
        //$users = collect($event->reply->mentionedUsers())
        //    ->map(function ($name) {
        //        return User::whereName($name)->first();
        //    })
        //    ->filter() //STRIP ALL NULL VALUES   //->filter(function ($miko) {return $miko->name == 'Fiso';})
        //    ->each(function ($user) use ($event) {
        //        $user->notify(new YouWereMentioned($event->reply));
        //    });
        
        User::whereIn('name', $event->reply->mentionedUsers())
            ->get()
            ->each(function ($user) use ($event) {  //au lieu d'un foreach!
                $user->notify(new YouWereMentioned($event->reply));
            });
    }
}
