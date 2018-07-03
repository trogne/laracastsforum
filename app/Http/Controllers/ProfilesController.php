<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Thread;
use App\Activity;

class ProfilesController extends Controller
{
    public function show(User $user)
    {
        //$threads = Thread::latest()->where('user_id', $user->id)->get();
        
        //$activities = $user->activity()->latest()->with('subject')->get()->groupBy('type');
        //$activities = $user->activity()->latest()->with('subject')->take(10)->get()->groupBy(function ($activity){ //pass a closure
        //    return $activity->created_at->format('Y-m-d');
        //});
    
        return view('profiles.show', [
            'profileUser' => $user,
            //'activities' => $this->getActivity($user)
            'activities' => Activity::feed($user)
        ]);
    }
    
    //public function getActivity(User $user)
    //{
    //    return $user->activity()->latest()->with('subject')->take(10)->get()->groupBy(function ($activity){ return $activity->created_at->format('Y-m-d'); //pass a closure
    //        });
    //}
}
