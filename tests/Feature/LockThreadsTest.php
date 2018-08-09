<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockThreads extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function non_administrators_may_not_lock_threads()
    {
        $this->withExceptionHandling(); //so that we can convert that to the proper response code
        
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        
        //$this->patch($thread->path(), [
        //    'locked' => true
        //])->assertStatus(403);
        //$this->post(route('locked-threads.store', $thread))->assertRedirect('threads');
        $this->post(route('locked-threads.store', $thread))->assertStatus(403);
        
        $this->assertFalse(!! $thread->fresh()->locked); //0 or 1 by default, unless we use model casting
    }
    
    /** @test */
    function administrators_can_lock_threads()
    {
        //$this->signIn(create('App\User', ['name' => 'JohnDoe']));
        $this->signIn(factory('App\User')->states('administrator')->create());
        
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        
        //$this->patch($thread->path(), [
        //    'locked' => true
        //]);
        $this->post(route('locked-threads.store', $thread));
        
        //$this->assertTrue(!! $thread->fresh()->locked, 'Failed asserting that the thread is locked.');
        $this->assertTrue($thread->fresh()->locked, 'Failed asserting that the thread is locked.'); //now cast to a boolean in Thread.php
    }
    
    /** @test */
    function administrators_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());
        
        $thread = create('App\Thread', ['user_id' => auth()->id(), 'locked' => true]);
        
        $this->delete(route('locked-threads.destroy', $thread));
        
        $this->assertFalse($thread->fresh()->locked, 'Failed asserting that the thread is unlocked.');
    }
    
    /** @test */    
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();
        
        //$thread = create('App\Thread');
        //$thread->lock();
        $thread = create('App\Thread', ['locked' => true]);
        
        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
