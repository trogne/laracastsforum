<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reply;

class BestRepliesController extends Controller
{
    public function store(Reply $reply)
    {
        //abort_if((int)$reply->thread->user_id !== auth()->id(), 401);
        $this->authorize('update', $reply->thread); //policy    
        
        //$reply->thread->update(['best_reply_id' => $reply->id]);
        $reply->thread->markBestReply($reply);
    }
}
