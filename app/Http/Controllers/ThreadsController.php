<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\{Thread, Channel};
//use App\Inspections\Spam;
use App\Rules\SpamFree;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Redis;
use App\Trending;

class ThreadsController extends Controller
{
    protected $trending;
    
    public function __construct()
    {
        //$this->middleware('auth')->only(['create', 'store']);
        $this->middleware('auth')->except(['index', 'show']);
        //$this->trending = new Trending;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */  
    //public function index($channelSlug = null)
    //public function index(Channel $channel)
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending) //ThreadFilters injected
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

        //$threadsByFiso = Thread::where('user_id', 31)->latest()->get();
        
        if(request()->wantsJson()){
            //dd(request()->header('accept')); //"application/json"
            //return $threads->sortByDesc('replies_count'); //orderBy('replies_count', 'desc') MARCHE PAS avec collections
            return $threads;
        }
        
        //$trending = Redis::zrevrange('trending_threads', 0, -1, 'WITHSCORES');
        //foreach($trending as $thread => $score) {
        //    echo json_decode($thread)->title, ' ', $score, '<br>';
        //}
        //die();
        //$trending = collect(Redis::zrevrange('trending_threads', 0, 4))->map(function ($thread) { //zrevrange returns an array of json encoded strings //  let's collect that into an illuminate collection (sinon: Call to a member function map() on array)
        //    return json_decode($thread);
        //});
        
        //$trending = array_map('json_decode', Redis::zrevrange('trending_threads', 0, 4)); // pass each value to json_decode
        ////Now in Trending.php
        
        //return view('threads.index', compact('threads')); //compact('threads','threadsByFiso')
        //return view('threads.index', compact('threads', 'trending'));
        //return view('threads.index', compact('threads', 'trending', 'popular'));
        return view('threads.index', [
            'threads' => $threads,
            'popular' => request()->has('popular'),            
            'trending' => $trending->get()
        ]);
    
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
        //dd(get_class(Thread::latest())); //Illuminate\Database\Eloquent\Builder
        
        $threads = Thread::latest()->filter($filters); 
        if($channel->exists){
            $threads->where('channel_id', $channel->id);
        }
        
        //dd($threads->toSql());
        //if(request()->wantsJson()){ //pour test, doit ajouté depuis pagination.  Mais non! extract only data from pagination object
        //    return $threads->get();
        //}
        
        //return $threads->get();
        return $threads->paginate(25);
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
    //public function store(Request $request, Spam $spam)
    public function store(Request $request)
    {
        //dd($request->all());
        //dd(auth()); //Illuminate\Auth\AuthManager    (auth=alias=facade, voir config/app.php)
        
        //$spam->detect(request('body'));
        //$this->validate($request, [
        //    'title' => 'required|spamfree',
        //    'body' => 'required|spamfree',
        //    'channel_id' => 'required|exists:channels,id'
        //]);
        //request()->validate([
        //    'title' => 'required|spamfree',
        //    'body' => 'required|spamfree',
        //    'channel_id' => 'required|exists:channels,id'
        //]); //5.5
        
        ////now middleware RedirectEmailNotConfirmed.php
        //if(!auth()->user()->confirmed) {
        //    return redirect('/threads')->with('flash', 'You must first confirm your email address.');
        //}
        
        request()->validate([
            'title' => ['required', new SpamFree], //that way, no need change AppServiceProvider and resources\lang\en\validation.php
            'body' => ['required', new SpamFree],
            'channel_id' => 'required|exists:channels,id'
        ]); //5.5
        
        ////custom mutator instead
        //$slug = str_slug(request('title'));
        //if (Thread::where('slug', $slug)->exists()) {
        //    $slug = $slug . '-2';
        //}
        
        $thread = Thread::create([
            'user_id' => auth()->id(), //ou request('user_id'),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
            //'slug' => str_slug(request('title'))
            //'slug' => request('title') //NOW done by the model event  //custom mutator...
        ]);
        
        if (request()->wantsJson()) {
            return response($thread, 201);
        }
        
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
    public function show($channel, Thread $thread, Trending $trending) //doing nothing with the channel here
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

        //////user->visits()->create($thread->id) //cache au lieu de hasOne relationship :
        //$key = sprintf('users.%s.visits.%s', auth()->id(), $thread->id);
        ////\Cache::rememberForever($key, function () { //NO - retrieves from cache if exist, and only if not, callback runs
        ////    return Carbon::now();
        ////});
        //cache()->forever($key, \Carbon\Carbon::now()); //replaces cache value each time 
        if(auth()->check()) {
            auth()->user()->read($thread);
        }

        //Redis::zincrby('trending_threads', 1, $thread->id); //non
        //Redis::zincrby('trending_threads', 1, json_encode([
        //    'title' => $thread->title,
        //    'path' => $thread->path()
        //]));
        ////now in Trending.php
        //$this->trending->push($thread); //using constructor injection
        $trending->push($thread); //using dependency injection (typehint)
        
        //$thread->recordVisit();
        //$thread->visits()->record();
        //$thread->visits()->record();
        //$thread->update(['visits' => (int)$thread->visits + 1]);
        $thread->increment('visits');
        
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
    //public function update(Request $request, Thread $thread)
    //public function update(Request $request, $channel, Thread $thread)
    public function update($channel, Thread $thread)
    {
        ////graduated to its own LockedThreadsController
        //if (request()->has('locked')) { //or a custom controller, like a LockThreadsController
        //    if (! auth()->user()->isAdmin()) { //ou request()->user()
        //        return response('', 403);
        //    }
        //    
        //    $thread->lock();
        //}
        
        
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
            return response([], 204); // 204 No Content
        }
        
        return redirect('/threads');
    }
}
