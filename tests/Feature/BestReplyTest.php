<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Support\Facades\DB;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function a_thread_creator_mark_any_reply_as_the_best_reply()
    {
        $this->withExceptionHandling();
        
        $this->signIn();
        
        //$thread = ThreadWithReplies::create(); // dedicated custom factory class
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id  ], 2);
        
        $this->assertFalse($replies[1]->isBest());

        //$this->postJson(route('best-replies.store', ['reply' => $replies[1]])); //can pass full reply or the id
        $this->postJson(route('best-replies.store', [$replies[1]->id]));
        
        $this->assertTrue($replies[1]->fresh()->isBest());
    }
    
    /** @test */
    function only_the_thread_creator_may_mark_a_reply_as_best()
    {
        $this->withExceptionHandling();
        
        $this->signIn();
        
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id  ], 2);
                
        //$this->signIn(create('App\User')); //pas besoin de passer create!
        $this->signIn(); //login a different user
        //dd(\App\User::get()->pluck('name'));
        
        $this->postJson(route('best-replies.store', [$replies[1]->id]))->assertStatus(403); //403=HTTP_FORBIDDEN;  401=HTTP_UNAUTHORIZED
        
        $this->assertFalse($replies[1]->fresh()->isBest());
    }
    
    /** @test */
    function if_a_best_reply_is_deleted_then_the_thread_is_properly_updated_to_reflect_that()
    {
        //DB::statement('PRAGMA foreign_keys=on'); //now on TestCase //to enforce the constraint on sqlite  (on or 1)
        
        $this->signIn();
        
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        
        $reply->thread->markBestReply($reply);
        
        //$this->assertTrue($reply->isBest);
        
        //$this->delete(route('replies.destroy', $reply)); //works too
        $this->deleteJson(route('replies.destroy', $reply)); //is pass the eloquent model, laravel will properly fetch the primary key off of it
        
        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
} 

//class ThreadWithReplies
//{
//    public static function create()
//    {
//        $thread = create('App\Thread');
//        create('App\Reply', ['thread_id' => $thread->id  ], 2);
//        return $thread;
//    }
//}
