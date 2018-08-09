<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Reply;
//use App\User;
//use App\Inspections\Spam;
use App\Rules\SpamFree;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\CreatePostRequest; // form request classes: when typehint this, laravel's gonna pick up on that and it'll automatically trigger the validation,  so no longer are we catching the exception and rendering a custom response
//use App\Notifications\YouWereMentioned;

class RepliesController extends Controller
{
    //protected $spam;
    
    public function __construct()
    {
        //$this->middleware('auth')->except<('index');
        $this->middleware('auth', ['except' => 'index' ]);
        //$this->spam =  app(Spam::class);
    }
    
    public function index($channelId, Thread $thread)
    {
        //return $thread->replies()->paginate(3, ['user_id', 'body'], 'sida');
        //return $thread->replies()->paginate(3, ['*'], 'sida');
        return $thread->replies()->paginate(20);
    }
    
    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        //Reply::create([...]); //no, we are adding a reply to a thread
        //$thread->addReply(request(['body']));
        //dd(request()); //Illuminate\Http\Request

        //preg_match('/oo CustOmEr supPOrt/i', request('body'), $matches, PREG_OFFSET_CAPTURE, 3);
        //preg_match_all('/y|a/i', request('body'), $matches);
        //dd($matches);
        ////if(str_contains(request('body'), 'Yahoo Customer Support')) { //laravel helper
        //if(stripos(request('body'), 'yaHoo CustOmEr supPOrt') !==false) {
        //    throw new \Exception('Your reply contains spam.');
        //}

        //$this->validate(request(), ['body' => 'required']);        
        //$spam->detect(request('body'));
        
        //$this->authorize('create', new Reply); //we need to pass a reply instance to trigger the policy method
        //if(Gate::denies('create', new Reply)) {
        //    return response(
        //        'Your are posting too frequently. Please take a break. :)', 422 //"This action is unauthorized."
        //    );
        //}
        ////authorization now in CreatePostForm
        
        //try {
        //    //$this->validateReply();
        //    //$this->validate(request(), ['body' => 'required|spamfree']);
        //    //request()->validate(['body' => 'required|spamfree']); //5.5
        //    
        //    //request()->validate(['body' => ['required', new SpamFree]]); //5.5
        //    ////validation now in CreatePostForm
        //
        //    //$lastReply = Reply::where('user_id', auth()->id())->latest()->first();
        //
        //    $reply = $thread->addReply([
        //        'body' => request('body'),
        //        'user_id' => auth()->id()
        //    ]);
        //} catch (\Exception $e){
        //    $errors = '';    //ValidationException, but I want the custom message (not default getMessage) .  AuthorizationException has no $e->errors()
        //    //foreach(collect($e->errors()) as $error) { //errors method returns 'messages' = instance of MessageBag
        //    //    $errors .= $error[0];
        //    //}
        //    foreach($e->errors() as $key => $value) { //errors method returns 'messages' = instance of MessageBag
        //        $errors .= $value[0];
        //    }
        //    return response(
        //        //'Sorry, your reply could not be saved at this time. - '.$e->getMessage(), 422
        //        'Sorry, your reply could not be saved at this time. - '.$errors, 422
        //    ); //422 = unprocessable entity
        //}
        
        ////5.5 NO TRY CATCH :
        ////request()->validate(['body' => ['required', new SpamFree]]); //5.5 //now validation handled with CreatePostFOrm form request class
        //$reply = $thread->addReply([
        //    'body' => request('body'),
        //    'user_id' => auth()->id()
        //]);        
        //
        ////if(request()->expectsJson()){
        //    return $reply->load('owner'); //not with('owner'), that's for the model
        ////}
        //////return redirect($thread->path());
        ////return back()->with('flash', 'Your reply has been left.'); // view:  session('flash'), not $flash

        //return $form->persist($thread); //another option, with  App\Http\Forms\CreatePostForm;
        
        ////NOW IN NotifyMentionedSubscribers listener :
        //$reply = $thread->addReply([
        //    'body' => request('body'),
        //    'user_id' => auth()->id()
        //]);
        //preg_match_all('/\@([^\s\.]+)/', $reply->body, $matches);
        //
        //foreach($matches[1] as $name) {
        //    $user = User::whereName($name)->first();
        //    
        //    if($user) {
        //        $user->notify(new YouWereMentioned($reply));
        //    }
        //}
        //return $reply->load('owner');

        if ($thread->locked) {
            return response('Thread is locked', 422);
        } //instead of at addReply
        
        //try { //no need try/catch if cataches in handler  (exception handling)
        //    return $thread->addReply([
        //        'body' => request('body'),
        //        'user_id' => auth()->id()
        //    ])->load('owner');            
        //} catch (\Exception $e){
        //    return response('Thread is locked', 422);            
        //}
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner'); //eager load the owner, and returned that as json
    }
    
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        
        //try {
        //    //$this->validate(request(), ['body' => 'required']);        
        //    //$spam->detect(request('body'));
        //    //$this->validateReply();
        //    //$this->validate(request(), ['body' => 'required|spamfree']);
        //    //request()->validate(['body' => 'required|spamfree']); //5.5
        //    request()->validate(['body' => [required, new SpamFree]]); //5.5
        //    
        //    //$reply->update(['body' => request('body')]);
        //    $reply->update(request(['body']));            
        //} catch (\Exception $e){
        //    return response(
        //        'Sorry, your update could not be saved - '.$e->getMessage(), 422
        //    );            
        //}
        
        //NO TRY CATCH
        //request()->validate(['body' => 'required|spamfree']); //lui, 5.4
        request()->validate(['body' => ['required', new SpamFree]]); //5.5
        
        $reply->update(request(['body']));
    }
    
    ////public function validateReply(Spam $spam)
    //public function validateReply()
    //{
    //    ////$this->spam->detect(request('body')); //if constructor
    //    ////app(Spam::class)->detect(request('body')); //container,   au lieu de (new Spam)
    //    //$this->validate(request(), ['body' => 'required']);
    //    //resolve(Spam::class)->detect(request('body'));
    //    $this->validate(request(), ['body' => 'required|spamfree']);
    //}

    public function destroy(Reply $reply)
    {
        //dd($reply->user_id); //"1" !! pourquoi not 1 ?!?!?
        //if($reply->user_id !== auth()->id()){
        //if((int)$reply->user_id !== auth()->id()){ //now a policy
        //    return response([], 403);
        //}
        $this->authorize('update', $reply); //reply policy

        $reply->delete();

        if(request()->expectsJson()){ //using axios  (sinon back et thread aussi deleted!!!!)
            return response(['status' => 'Reply deleted']);
        }
        
        return back();
    }
}
