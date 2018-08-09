<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use App\Scopes\AgeScope;
use App\Thread;
use App\Reply;
use App\Activity;

class User extends Authenticatable
{
    use Notifiable;

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::addGlobalScope(new AgeScope);
//    }    
//////and in AgeScope class :
//    //public function apply(Builder $builder, Model $model)
//    //{
//    //    $builder->where('age', '>', 200);
//    //}
//////then User::all() =   select * from `users` where `age` > 200

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protecterd $guarded = []; //don't guard anything... accept all
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    protected $casts = [ //cast this string to json, or that number to a boolean
        'confirmed' => 'boolean'
    ];
    
    public function getRouteKeyName()
    {
        return 'name';
    }
    
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }
    
    public function lastReply()
    {
        //dd($this->hasOne(Reply::class)->get()); //all of them, same as hasMany
        return $this->hasOne(Reply::class)->latest();
    }
    
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }
    
    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;
        $this->save();
    }
    
    public function isAdmin() //instead of a dedicated roles table
    {
        return in_array($this->name, ['JohnDoe', 'JaneDoe', 'Fiso']);
    }
    
    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            \Carbon\Carbon::now()
        );
    }
    
    public function visitedThreadCacheKey($thread)
    {
        return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }
    
    //public function avatar()
    //{
    //    //if(!$this->avatar_path) {
    //    //    return 'avatars/default.jpg';
    //    //}
    //    //return $this->avatar_path;
    //
    //    //return $this->avatar_path ?: 'avatars/default.jpg';
    //    return asset($this->avatar_path ?: 'images/avatars/default.png');
    //}

    //public function getAvatarAttribute() //custom accessor
    //{
    //    return asset($this->avatar_path ?: 'avatars/default.jpg');
    //}
    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ?: 'images/avatars/default.png');
    }
}
