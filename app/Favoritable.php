<?php

namespace App;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function($model) {
            // $model->favorites()->delete(); //NON
            //$model->favorites->each(function($favorite) {
            //    $favorite->delete();
            //});
            $model->favorites->each->delete();
        });        
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited'); //polymorphic one-to-many relationship
    }
    
    public function favorite()
    {
        //if(! $this->favorites()->where('user_id', auth()->id())->exists()){
        //if(! $this->favorites()->where(['user_id' => auth()->id()])->exists()){
        //    $this->favorites()->create(['user_id' => auth()->id()]);
        //}
        $attributes = ['user_id' => auth()->id()];
        if(! $this->favorites()->where($attributes)->exists()){
            return $this->favorites()->create($attributes);
        }
    }
    
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];
        
        //$this->favorites()->where($attributes)->delete(); //just an sql query with the query builder, at no point do we build up any favorite instances
        //$this->favorites()->where($attributes)->each(function ($favorite){
        //    $favorite->delete();
        //});
        $this->favorites()->where($attributes)->get()->each->delete();  //delete on the model   //higher-order collections
    }
    
    public function isFavorited()
    {
        //return $this->favorites()->where('user_id', auth()->id())->exists();
        return !! $this->favorites->where('user_id', auth()->id())->count();  //!! = cast to a boolean
    }
    
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }
    
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
