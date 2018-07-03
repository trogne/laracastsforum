<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
//use Illuminate\Http\Request;

class RepliesController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth')->except<('index');
        $this->middleware('auth', ['except' => 'index' ]);
    }
    
    public function index($channelId, Thread $thread)
    {
        //return $thread->replies()->paginate(3, ['user_id', 'body'], 'sida');
        //return $thread->replies()->paginate(3, ['*'], 'sida');
        return $thread->replies()->paginate(20);
    }
    
    public function store($channelId, Thread $thread)
    {
        //Reply::create([...]); //no, we are adding a reply to a thread
        //$thread->addReply(request(['body']));
        //dd(request()); //Illuminate\Http\Request
        $this->validate(request(), [
            'body' => 'required'
        ]);        
        
        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        if(request()->expectsJson()){
            return $reply->load('owner'); //not with('owner'), that's for the model
        }
        //return redirect($thread->path());
        return back()->with('flash', 'Your reply has been left.'); // view:  session('flash'), not $flash
    }
    
    public function destroy(Reply $reply)
    {
        //dd($reply->user_id); //"1" !! pourquoi not 1 ?!?!?
        //if($reply->user_id !== auth()->id()){
        //if((int)$reply->user_id !== auth()->id()){ //now a policy
        //    return response([], 403);
        //}
        $this->authorize('update', $reply); //reply policy

        $reply->delete();

        if(request()->expectsJson()){ //using axios  (sinon back et thread aussi deleted!!!!)
            return response(['status' => 'Reply deleted']);
        }
        
        return back();
    }
    
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
                
        //$reply->update(['body' => request('body')]);
        $reply->update(request(['body']));
    }
}
