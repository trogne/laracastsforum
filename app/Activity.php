<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];
    
    public function subject()  //name important
    {
        //return $this->morphTo('subject', 'subject_type');
        return $this->morphTo();
    }
    
    public static function feed($user, $take = 50)
    {
        //return $user->activity()  //don't even have to reference the activity relationship on user
        return static::where('user_id', $user->id) // = Activity::where   //works with self too
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(function ($activity){
                return $activity->created_at->format('Y-m-d');
            }); //pass a closure
    }
}
