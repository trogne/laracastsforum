<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\ThreadFilters;
use App\RecordsActivity;
use App\ThreadSubscription;
use App\Notifications\ThreadWasUpdated;
use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
//use Illuminate\Support\Facades\Redis;
//use App\RecordsVisits;
//use App\Visits;
use App\Reply;
//use App\Exceptions\ThreadIsLockedException;
use Laravel\Scout\Searchable;
//use Stevebauman\Purify\Purify; //NO!!!!!!!!!!
//use Stevebauman\Purify\Facades\Purify;

class Thread extends Model
{
    //use RecordsActivity, RecordsVisits;
    use RecordsActivity, Searchable;
    
    protected $purify;
    
    ////pas rap :)
    //public function __construct(array $attributes = array())
    //{
    //    //dd($attributes); // __construct method called with all the attributes, so I have to pass that to the parent 
    //    parent::__construct($attributes);
    //
    //    $this->purify = new Purify;
    //}    
    
    protected $guarded = [];
    
    protected $with = ['creator', 'channel'];


    protected $appends = ['isSubscribedTo'];
    
    protected $casts = [
        'locked' => 'boolean'
    ];
    
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

        static::created(function ($thread){
            //$thread->slug = $thread->title;
            //$thread->save();
            $thread->update(['slug' => $thread->title]);
        });
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
        //return "/threads/{$this->channel->slug}/{$this->id}";
        return "/threads/{$this->channel->slug}/{$this->slug}";
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
        
        //(new \App\Spam)->detect($reply->body);

        //if ($this->locked) {
        //    //throw new ThreadIsLockedException('miko');
        //    throw new \Exception('Thread is locked');
        //}
        
        $reply = $this->replies()->create($reply); //auto apply thread id in the process
        
        event(new ThreadReceivedNewReply($reply)); //we make an announcement

        //if(auth()->check()) {  //cause I just add a reply, so I don't want the thread title to be strong.  So I reupdate the read at time after reply creation
        //    auth()->user()->read($this);
        //}
        
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
        
        //$this->subscriptions
        //    //->filter(function($sub) use ($reply) {  ////a more collection approach    //collection pipeline
        //    //    return $sub->user->id != $reply->user_id;
        //    //})
        //    ->where('user_id', '!=', $reply->user_id) //ASDASD
        //    //->each(function($sub) use ($reply) {
        //    //    $sub->user->notify(new ThreadWasUpdated($this, $reply));
        //    //});
        //    //->each(function($sub) use ($reply) {
        //    //    $sub->notify($reply);
        //    //});            
        //    ->each
        //    ->notify($reply); //higher order collection
        ////firing an event instead (logic in listener) :
        
        //event(new ThreadHasNewReply($this, $reply)); //listener added to ThreadReceivedNewReply
        //$this->notifySubscribers($reply);

        return $reply;
    }
    
    ////now directly in controller
    //public function lock()
    //{
    //    $this->update(['locked' => true]);
    //}
    //
    //public function unlock()
    //{
    //    $this->update(['locked' => false]);
    //}
    
    //public function notifySubscribers($reply)
    //{
    //    $this->subscriptions
    //        ->where('user_id', '!=', $reply->user_id)
    //        ->each
    //        ->notify($reply);        
    //}

    public function scopeFilter($query, ThreadFilters $filters) //query scope //query = query builder, Illuminate\Database\Eloquent\Builder
    {
        //return $query->where('id','>',50);
        return $filters->apply($query); //apply those filters to the current thread query we have running
    }                                   //when we call the filter method on thread, that will ask our ThreadFlters class to apply itself to the query

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
    
    public function hasUpdatesFor($user)
    {
        //$key = sprintf('users.%s.visits.%s', auth()->id(), $this->id);
        $key = $user->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function setSlugAttribute($value, $count = 2) //custom mutator
    {
        //if(static::whereSlug($slug = str_slug($value))->exists()) {
        //    //$this->attributes['slug'] = $slug . '-2';
        //    $slug = $this->incrementSlug($slug);
        //}
        //
        //$this->attributes['slug'] = $slug;
 
        $slug = str_slug($value);
        
        //$original = $slug; //we cache the value
        //while (static::whereSlug($slug)->exists()) { //potential n+1 problem, but really not that much of an issue
        //    $slug = "{$original}-" . $count++; //incremented AFTER assignement,   ++$count  
        //}
        ////BY ID:  //HAVE access to ID cause model event "created" triggered AFTER creation
        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id; //$this->created_at->timestamp; //md5($this->id)
        }
        
        $this->attributes['slug'] = $slug;
        
        //aussi : /threads/channel/89/the-slug-of-the-thread (non unique slug, just for descriptive purposes)
    }

    public function markBestReply(Reply $reply)
    {
        //$this->best_reply_id = $reply->id;
        //$this->save();
        $this->update(['best_reply_id' => $reply->id]);
    }
    
    //public function incremenctSlug($slug, $count = 2)
    //{
    //    ////static::whereTitle($this->title)->max('slug');
    //    //$max = static::whereTitle($this->title)->latest('id')->value('slug');
    //    //
    //    ////if (is_numeric(substr($max, -1, 1)))
    //    //if (is_numeric($max[-1])) { //php7, take a string, and interact with it as an array
    //    //    return preg_replace_callback('/(\d+)$/', function ($matches) {
    //    //        return $matches[1] + 1;
    //    //    }, $max);  // increment the match inside the string in place
    //    //}
    //    //
    //    //return "{$slug}-2";
    //    
    //    $original = $slug;
    //    
    //    while (static::whereSlug($slug)->exists()) { //potential n+1 problem, but really not that much of an issue
    //        $slug = "{$original}-" . $count++; //incremented AFTER assignement,   ++$count  
    //    }
    //    
    //    return $slug;
    //}
    
    ////now in a RecordsVisits trait
    //public function recordVisit()
    //{
    //    Redis::incr($this->visitsCacheKey());
    //    
    //    return $this;
    //}
    //
    //public function visits()
    //{
    //    return Redis::get($this->visitsCacheKey());
    //}
    //
    //public function resetVisits()
    //{
    //    Redis::del($this->visitsCacheKey());
    //}
    //
    //public function visitsCacheKey()
    //{
    //    return "threads.{$this->id}.visits";
    //}
    
    ////no more a trait, since only one method after extracting to class Visits
    ////but now no more Redis at all :)
    //public function visits()
    //{
    //    //return Redis::get($this->visitsCacheKey()) ?? 0; // ?? = null coalesce operator     ?? vs ?:   
    //    return new Visits($this);
    //}
    
    public function toSearchableArray() //override method from Searchable trait
    {
        return $this->toArray() + ['path' => $this->path()];
    }
    
    public function getBodyAttribute($body) //custom accessor
    {
        ////using __construct : 
        //return Purify::clean($body); //no, Purify::clean() should not be called statically
        //return $this->purify->clean($body);
        ////without __construct :
        return \Purify::clean($body); //works, maybe because \Purify is already instantiated
    }    
}
