<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits //first-class citizen
{
    protected $thread;
    
    public function __construct($thread)
    {
        $this->thread = $thread;
    }
    
    public function reset($thread)
    {
        Redis::del($this->cacheKey());
        return $this;
    }
    
    public function record($thread)
    {
        Redis::incr($this->cacheKey());
        return $this;
    }
    
    public function count()
    {
        return (int)Redis::get($this->cacheKey()) ?? 0; // ?? = null coalesce operator     ?? vs ?:   
    }

    public function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }      
}