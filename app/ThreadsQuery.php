<?php

//c'est juste moi qui teste...
namespace App;
use App\Channel;

class ThreadsQuery
{
    protected $mix;
    
    public function __construct()
    {
        $this->mix = 'maasodija';
    }
    
    public function get(Channel $channel)
    {
        //return $this->mix;
        //$userId = \App\User::get()->where('name', 'Fiso')->first()->id;
        //return \App\Thread::get()->where('user_id', $userId);

        if($channel->exists){
            $threads = $channel->threads()->latest();
        } else {
            $threads = Thread::latest();
        }
        
        if($username = request('by')){
            $user = \App\User::where('name', $username)->firstOrFail();
            $threads->where('user_id', $user->id);
        }
        
        $threads = $threads->get();
        return $threads;        
    }
}
