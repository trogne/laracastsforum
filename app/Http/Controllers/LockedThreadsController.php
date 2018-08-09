<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class LockedThreadsController extends Controller
{
    public function store(Thread $thread)
    {
        ////dd($thread->creator->name);
        ////now admin middleware        
        //if (! auth()->user()->isAdmin()) { 
        //    return response('You do not have permission to lock this thread.', 403);
        //}
        
        //$thread->lock();
        $thread->update(['locked' => true]);
    }
    
    public function destroy(Thread $thread)
    {
        //$thread->unlock();
        $thread->update(['locked' => false]);
    }
}

//lock method on laravel's query builder