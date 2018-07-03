<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\RecordsActivity;

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
    
    protected $appends = ['favoritesCount', 'isFavorited']; //included when cast to json

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($reply){ //model event
            $reply->thread->increment('replies_count');
        });
        
        static::deleted(function ($reply){
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

    public function path()
    {
        return $this->thread->path() . '#reply-' . $this->id;
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
}
