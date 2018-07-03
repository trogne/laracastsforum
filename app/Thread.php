<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\ThreadFilters;
use App\RecordsActivity;
use App\ThreadSubscription;
use App\Notifications\ThreadWasUpdated;

class Thread extends Model
{
    use RecordsActivity;
    
    protected $guarded = [];
    
    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    ////public function replyCount()
    //public function geteplyCountAttribute() //custom getter
    //{
    //    return $this->replies()->count();
    //}
    ////global query scope instead : 
    protected static function boot()
    {
        parent::boot();
        
        ////static::addGlobalScope(new AgeScope);  // ... apply(Builder $builder...    
        //static::addGlobalScope('replyCount', function ($builder){
        //    $builder->withCount('replies');
        //}); //global scope is just a query scope that is automatically applied to all the queries
        ////static::addGlobalScope('creator', function ($builder){
        ////    $builder->with('creator');
        ////});
        ////NOW STORED AS A COLUMN
        
        static::deleting(function ($thread){   //model event
            //$thread->replies()->delete(); //deletes all replies
            //$thread->replies->each(function ($reply){
            //    $reply->delete();   //triggers activity delete for each reply
            //});
            $thread->replies->each->delete(); //higher-order messaging for collections
        });
        
        //static::created(function ($thread){
        //    Activity::create([
        //        'user_id' => auth()->id(),
        //        'type' => 'created_thread',
        //        'subject_id' => $thread->id,
        //        'subject_type' => 'App\Thread'                
        //    ]);
        //});
        
        //static::created(function ($thread){  //moved to trait bootRecordsActivity
        //    $thread->recordActivity('created');
        //});        
    }

    ////to trait:
    //protected function recordActivity($event)
    //{
    //    Activity::create([
    //        'user_id' => auth()->id(),
    //        'type' => $this->getActivityType($event),  //->name = //App\Thread
    //        'subject_id' => $this->id,
    //        'subject_type' => get_class($this)
    //    ]);
    //}
    //
    //function getActivityType($event)
    //{
    //    return $event . '_' . strtolower((new \ReflectionClass($this))->getShortName());
    //}
            //foreach((new \ReflectionClass($thread))->getMethods() as $method){
            //    //var_dump($method->class, $method->name);
            //    if($method->class == 'App\Thread'){
            //        echo $method->name . '<br>';
            //    }
            //};
            //die();
            
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        //return $this->hasMany(Reply::class, 'thread_id');
        //return $this->hasMany(Reply::class)
        //    ->withCount('favorites')
        //    ->with('owner'); //eager load the owner // now eager loaded at reply level
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    
    //public function creatorName()
    //{
    //    return $this->creator->name;
    //}
    
    public function addReply($reply)
    {
        //return $this->replies()->create($reply); //auto apply thread id in the process

        //$reply = $this->replies()->create($reply);
        //$this->increment('replies_count');    //$this->decrement('replies_count');
        //return $reply;
        ////NOW boot on Reply.php
        
        $reply = $this->replies()->create($reply); //auto apply thread id in the process
        
        //foreach($this->subscriptions as $subscription) {
        //    if($subscription->user->id != $reply->user_id) {
        //        $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        //    }
        //}

        //$subscriptions = $this->subscriptions->filter(function($sub) use ($reply) {
        //    return $sub->user->id != $reply->user_id;
        //});
        //foreach($subscriptions as $subscription) {
        //    $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        //}
        
        $this->subscriptions
            //->filter(function($sub) use ($reply) {  ////a more collection approach    //collection pipeline
            //    return $sub->user->id != $reply->user_id;
            //})
            ->where('user_id', '!=', $reply->user_id) //ASDASD
            //->each(function($sub) use ($reply) {
            //    $sub->user->notify(new ThreadWasUpdated($this, $reply));
            //});
            //->each(function($sub) use ($reply) {
            //    $sub->notify($reply);
            //});            
            ->each
            ->notify($reply); //higher order collection
        
        return $reply;
    }

    public function scopeFilter($query, ThreadFilters $filters) //query scope //query = query builder, Illuminate\Database\Eloquent\Builder
    {
        //return $query->where('id','>',50);
        return $filters->apply($query); //apply those filters to the current thread query we have running
    }

    //public function activity()
    //{
    //    return $this->morphMany(Activity::class, 'subject');
    //}
    
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
        
        return $this;
    }
    
    public function unsubscribe($userId = null)
    {
        //$this->subscriptions()->delete([
        //    'user_id' => $userId ?: auth()->id()
        //]);
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }
    
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }
    
    public function getIsSubscribedToAttribute()  // custom eloquent accessor
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }
}

//when we call the filter method on thread, that will ask our ThreadFlters class to apply itself to the query


