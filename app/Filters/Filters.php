<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters   //abstract = no instantiate directly... always instantiate a subclass
{
    protected $request, $builder;
    protected $filters = []; //not needed, overridden in subclass ThreadFilters.php
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function apply($builder)
    {
        $this->builder = $builder;
            
        //////more functional approach :  
        ////dd(collect($this->getFilters()));
        //$this->getFilters()
        //    //->filter(function ($value, $filter){
        //    ->filter(function ($filter){ //with flip()
        //        return method_exists($this, $filter); //continue if true
        //    })
        //    //->each(function ($value, $filter){
        //    ->each(function ($filter, $value){ //with flip()
        //        $this->$filter($value);
        //    });
        
        //if(! $username = $this->request->by) return $builder;
        //$user = User::where('name', $username)->firstOrFail();
        //return $builder->where('user_id', $user->id);
        
        ////return $this->by($username);
        //if ($this->request->has('by')) {
        //    $this->by($this->request->by);
        //}
        
        //dd($this->request->all());
        //dd($this->request->only($this->filters));
        
        //foreach($this->filters as $filter){
        //foreach($this->request->only($this->filters) as $filter){
        //foreach($this->getFilters() as $filter){
        
        foreach($this->getFilters() as $filter => $value){
            ////if (method_exists($this, $filter) && $this->request->has($filter)) {
            //if ($this->hasFilter($filter)) {
            //    $this->$filter($this->request->$filter);
            //}
            //if (!$this->hasFilter($filter)) return;
            //$this->$filter($this->request->$filter);
            if (method_exists($this, $filter)) {
                //$this->$filter($this->request->$filter);
                $this->$filter($value);
            }
        }
        //return $this->builder; //don't have to return the query builder
    }
    
    protected function getFilters()
    {
        //return array_keys($this->request->only($this->filters));
        //return $this->request->intersect($this->filters); //Method intersect does not exist. (removed in 5.5)
        return $this->request->only($this->filters);
        //var_dump(collect($this->request->only($this->filters))->flip()); //Illuminate\Support\Collection
        //return collect($this->request->only($this->filters)); //collect sinon :  Call to a member function filter() on array
        //return collect($this->request->only($this->filters))->flip();
    }
    
    //protected function hasFilter($filter): bool
    //{
    //    return method_exists($this, $filter) && $this->request->has($filter);
    //}    
}
