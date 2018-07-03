<?php

namespace App;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if(auth()->guest()) return;

        foreach(static::getActivitiesToRecord() as $event){
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
        
        static::deleting(function ($model){
            $model->activity()->delete();
        });
    }
    
    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }
    
    protected function recordActivity($event)
    {
        //Activity::create([
        //    'user_id' => auth()->id(),
        //    'type' => $this->getActivityType($event),  //->name = //App\Thread
        //    'subject_id' => $this->id,
        //    'subject_type' => get_class($this)
        //]);

        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
            //'created_at' => $this->created_at //testing it_fetches_a_feed_for_any_user
        ]);
    }
    
    //polymorphic methods that laravel provides
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject'); //like hasMany, but we're not hardcoding the related model
    }
    
    function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }    
}
