<?php

namespace App\Filters;

use App\User;
//use Illuminate\Http\Request;

class ThreadFilters extends Filters
{
    //public function __construct(Request $request)
    //{
    //    parent::__construct($request);
    //}    
    
    protected $filters = ['by', 'popular', 'unanswered'];
    
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        
        //return $this->builder->where('user_id', $user->id);        
        $this->builder->where('user_id', $user->id); //don't have to return
    }
    
    protected function popular()
    {
        //dd(get_class($this->builder->getQuery())); //Illuminate\Database\Query\Builder, au lieu de Illuminate\Database\Eloquent\Builder
        $this->builder->getQuery()->orders = [];
        //$this->builder->orderBy('replies_count', 'desc')->paginate(10);
        $this->builder->orderBy('replies_count', 'desc');
    }

    protected function unanswered()
    {
        $this->builder->where('replies_count', 0);
    }    
}
