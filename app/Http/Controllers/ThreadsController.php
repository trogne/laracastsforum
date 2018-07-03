<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\{Thread, Channel};
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth')->only(['create', 'store']);
        $this->middleware('auth')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */  
    //public function index($channelSlug = null)
    //public function index(Channel $channel)
    public function index(Channel $channel, ThreadFilters $filters) //ThreadFilters injected
    {
        ////$threads = (new \App\ThreadsQuery)->get($channel); //refactpr tp query object //c'est moi qui teste...
        ////$threads = $this->getThreads($channel); //refactor to protected function getThreads
        //
        //////we won't going to extract after all... : 
        //
        ////if($channelSlug){
        ////if($channel->exists){
        ////    ////$channelId = Channel::whereSlug($channelSlug)->first()->id;
        ////    ////$threads = Thread::where('channel_id', $channelId)->latest()->get();
        ////    //$channelId = $channel->id;
        ////    //$threads = Thread::where('channel_id', $channelId)->latest()->get();
        ////    //dd(get_class($channel->threads())); //Illuminate\Database\Eloquent\Relations\HasMany
        ////    //dd(get_class($channel->threads)); //Illuminate\Database\Eloquent\Collection"
        ////    $threads = $channel->threads()->latest();
        ////} else {
        ////    $threads = Thread::latest();
        ////}
        //
        ////$threads = Thread::latest();
        //$threads = Thread::latest()->filter($filters);
        //
        //if($channel->exists){
        //    $threads->where('channel_id', $channel->id);
        //}
        //
        ////if($username = request('by')){
        ////    $user = \App\User::where('name', $username)->firstOrFail();
        ////    $threads->where('user_id', $user->id);
        ////}
        //
        //////$threads = Thread::filter($filters)->get();
        ////$threads = $threads->filter($filters)->get(); //dd(get_class($threads)); //Illuminate\Database\Eloquent\Builder
        //$threads = $threads->get();

        $threads = $this->getThreads($channel, $filters);
        
        if(request()->wantsJson()){
            //dd(request()->header('accept')); //"application/json"
            //return $threads->sortByDesc('replies_count'); //orderBy('replies_count', 'desc') MARCHE PAS avec collections
            return $threads;
        }
        
        return view('threads.index', compact('threads'));
        //return view('threads.index', [
        //    'threads' => $threads,
        //    'popular' => request()->has('popular')
        //]);
    }
    
    //protected function getThreads(Channel $channel) {
    protected function getThreads(Channel $channel, ThreadFilters $filters) {
        //if($channel->exists){
        //    $threads = $channel->threads()->latest();
        //} else {
        //    $threads = Thread::latest();
        //}
        //
        //if($username = request('by')){
        //    $user = \App\User::where('name', $username)->firstOrFail();
        //    $threads->where('user_id', $user->id);
        //}
        //$threads = Thread::filter($filters);    //won't filter if no filters
        //$threads = Thread::latest()->filter($filters); 
        //$threads = Thread::with('channel')->latest()->filter($filters); 
        $threads = Thread::latest()->filter($filters); 
        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }
        
        //dd($threads->toSql());
        
        return $threads->get();

        //if(request()->wantsJson()){ //j'ai ajouté ça a cause de ma pagination
        //    return $threads->get();
        //}        
        //return $threads->paginate(10);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$channels = Channel::all();
        //return view('threads.create')->with('channels', $channels);
        //return view('threads.create')->with(compact('channels'));
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        //dd(auth()); //Illuminate\Auth\AuthManager    (auth=alias=facade, voir config/app.php)
        
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id'
        ]);
        
        $thread = Thread::create([
            'user_id' => auth()->id(), //ou request('user_id'),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
        ]);
        
        return redirect($thread->path())
            ->with('flash', 'Your thread has been published'); // view:  session('flash'), not $flash
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    //public function show(Thread $thread) //ROUTE MODEL BINDING
    public function show($channel, Thread $thread) //doing nothing with the channel here
    {
        //return $thread->load('creator')->load('replies');
        //return  $thread->withCount('replies')->first();
        //return Thread::withCount('replies')->first();
        //return Thread::withCount('replies')->get()->where('id', 53)->first();
        //return Thread::withCount('replies')->find(53);
        
        //return view('threads.show', compact('channel', 'thread'));
        //return view('threads.show', compact('thread'));
        //return $thread->load('replies.favorites'); //the replies AND the favorites
        
        //return $thread->find($thread->id);
        //return $thread->withoutGlobalScope('replyCount')->find($thread->id);
        
        //return $thread->replies();
        
        //return view('threads.show', [
        //    'thread' => $thread,
        //    'replies' => $thread->replies()->paginate(10)
        //]);
        
        //return $thread->append('isSubscribedTo'); // $appends in Threads model
        
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        //$thread->replies()->delete();    
        //$thread->delete();     //or could overwrite the delete method or use model events
        
        //if($thread->user_id != auth()->id()){ //now a policy
        //    //if(request()->wantsJson()){
        //    //    return response(['status' => 'Permission Denied'], 403);
        //    //}
        //    //
        //    //return redirect('/login');
        //    abort(403, 'You do not have permission to do this.');
        //}
        
        $this->authorize('update', $thread); //thread policy
        $thread->delete();      //model event
        
        if(request()->wantsJson()){     // dd($request->headers->get('accept'));
            return response([], 204);
        }
        
        return redirect('/threads');
    }
}
