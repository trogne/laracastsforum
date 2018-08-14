<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Thread, Trending};

class SearchController extends Controller
{
    public function show(Trending $trending)
    {                
        //return Thread::search(request('q'))->get();  //returns a "Laravel\Scout\Builder",  can search because added Searchable trait
        //return Thread::search(request('q'))->paginate(25);
        //$threads = Thread::search(request('q'))->paginate(25); //no longer returns a list of thread, it's actually a paginated object            
        
        if (request()->expectsJson()) {
            return Thread::search(request('q'))->paginate(25); //server-side search, otherwise we'll let it be handled on the client side
        }
        
        //return view('threads.index', [
        //    'threads' => $threads,
        //    'popular' => request()->has('popular'),            
        //    'trending' => $trending->get() //good candidate for a view composer, or a dedicated component where we just fetch the trending articles with ajax and that way it could all be done on the frontend
        //]);
        return view('threads.search', [
            'trending' => $trending->get()
        ]);        
    }
}
