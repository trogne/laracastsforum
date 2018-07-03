<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use App\Scopes\AgeScope;
use App\Thread;
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
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }
    
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }
    
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }
}
