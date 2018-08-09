<?php
//
//namespace App;
//
//use Illuminate\Support\Facades\Redis;
//use App\Visits;
//
//trait RecordsVisits
//{
//    //public function recordVisit()
//    //{
//    //    Redis::incr($this->visitsCacheKey());
//    //    return $this;
//    //}
//    
//    public function visits()
//    {
//        //return Redis::get($this->visitsCacheKey()) ?? 0; // ?? = null coalesce operator     ?? vs ?:   
//        return new Visits($this);
//    }
//    
//    //public function resetVisits()
//    //{
//    //    Redis::del($this->visitsCacheKey());
//    //}
//    
//    //public function visitsCacheKey()
//    //{
//    //    return "threads.{$this->id}.visits";
//    //}    
//}