<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\RecordsActivity;
use App\Inspections\Spam;
use Carbon\Carbon;

class Reply extends Model
{
    use Favoritable, RecordsActivity;
    
    //protected $fillable = ['body', 'user_id'];
    protected $guarded = [];
    
    //protected static function boot()
    //{
    //    parent::boot();
    //    static::addGlobalScope('owner', function ($builder){
    //        $builder->with('owner');
    //    });
    //    static::addGlobalScope('favorites', function ($builder){
    //        $builder->with('favorites');
    //    });        
    //}
    ////App\Reply::withoutGlobalScope('owner')->first()
    ////if never a condition where you want to disable a global scope:
    protected $with = ['owner', 'favorites']; //we want to eager load the owner and favorites for every single query
    
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest']; //included when cast to json //could also override the to_array method and do this manually

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($reply){ //model event
            $reply->thread->increment('replies_count');
        });
        
        static::deleted(function ($reply){
            //if ($reply->isBest()) { //database level instead
            //    $reply->thread->update(['best_reply_id' => null]);
            //}
            $reply->thread->decrement('replies_count');
        });
    }
    
    //protected $withCount = ['favorites']; //WORKS, but custom getter instead
    //protected static function boot()
    //{
    //    parent::boot();
    //    static::addGlobalScope('favoriteCount', function ($builder){
    //        $builder->withCount('favorites'); //problem with withCount and polymorphic relation
    //    });
    //}
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');  // user_id because foreign key is not owner_id
    }
    
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function mentionedUsers()
    {
        //preg_match_all('/\@([^\s\.]+)/', $this->body, $matches);
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);
        return $matches[1];
    }
    
    public function path()
    {
        return $this->thread->path() . '#reply-' . $this->id;
    }
    
    public function setBodyAttribute($body) //custom mutator
    {
        //preg_match('/(.*)(@([^\s\.]+))(.*)/', $body, $matches); //not a good idea
        //$this->attributes['body'] = $matches[1] . '<a href="/profiles/' . $matches[3] . '">' . $matches[2] . '</a>' . $matches[4];
    
        //$this->attributes['body'] = preg_replace('/@([^\s\.]+)/', '<a href="/profiles/$1">$0</a>', $body);
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body); // @[\w\-\.]+[\w]+  (to allow a dot, but no final dot!)
    }
    
    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }
    
    public function getIsBestAttribute()
    {
        return $this->isBest();
    }
    
    ////moved to trait Favoritable : 
    //public function favorites()
    //{
    //    return $this->morphMany(Favorite::class, 'favorited'); //polymorphic one-to-many relationship
    //}
    //
    //public function favorite()
    //{
    //    //if(! $this->favorites()->where('user_id', auth()->id())->exists()){
    //    //if(! $this->favorites()->where(['user_id' => auth()->id()])->exists()){
    //    //    $this->favorites()->create(['user_id' => auth()->id()]);
    //    //}
    //    $attributes = ['user_id' => auth()->id()];
    //    if(! $this->favorites()->where($attributes)->exists()){
    //        return $this->favorites()->create($attributes);
    //    }        
    //}
    //
    //public function isFavorited()
    //{
    //    //return $this->favorites()->where('user_id', auth()->id())->exists();
    //    return !! $this->favorites->where('user_id', auth()->id())->count();  //!! = cast to a boolean
    //}
    //
    //public function getFavoritesCountAttribute()
    //{
    //    return $this->favorites->count();
    //}
    
    //public function update(array $attributes = [], array $options = [])
    //{
    //    app(Spam::class)->detect($attributes['body']);
    //    parent::update($attributes);
    //}

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }  
}
