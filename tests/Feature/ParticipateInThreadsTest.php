<?php

namespace Tests\Feature; // Feature = outside-in tests

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInThreadsTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function unauthenticated_users_may_not_add_replies(){
        //$this->expectException(\Illuminate\Auth\AuthenticationException::class); //test passed cause that exception is thrown
        
        //$thread = factory('App\Thread')->create();
        //$reply = factory('App\Reply')->make();
        //$this->be($thread->creator); //Failed asserting that exception of type "Illuminate\Auth\AuthenticationException" is thrown.
        //$this->post($thread->path().'/replies', $reply->toArray());        
        //$this->post('/threads/1/replies', []);
        //$this->post('/threads/some-channel/1/replies', []);
        
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }    
    
    /** @test */
    function an_authenticated_user_may_participate_in_forum_threads(){

        ////$user = factory('App\User')->create();
        ////$this->signIn($user = factory ('App\User')->create());
        //$this->be($user = factory('App\User')->create()); //set the "currently logged in user" for the application
        //
        //$thread = factory('App\Thread')->create();
        //
        ////$reply = factory('App\Reply')->create(['thread_id' => $thread->id, 'user_id' => $thread->user_id]);
        //$reply = factory('App\Reply')->make(); //create() persist to DB, then post another reply below

        $this->signIn();
        $thread = create('App\Thread');
        $reply = make('App\Reply');
        
        $this->post($thread->path().'/replies', $reply->toArray());
        
        //$this->get($thread->path())
        //    ->assertSee($reply->body); //lui non, cause reply loaded with javascript (mais moi oui)
        $this->assertDatabaseHas('replies', ['body' => $reply->body]); //il fait donc ça
        $this->assertEquals(1, $thread->fresh()->replies_count); //fresh for fresh instance from db, because factory does not create a replies_count 
    }
    
    /** @test */
    function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();
        
        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => null]);
        
        return $this->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
            //->assertstatus(422); //if using try/catch, now not needed cause  if ($request->expectsJson()) in Handler,  donc pas catching... bubble up...
    }
    
    /** @test */
    function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();
        
        $reply = create('App\Reply');
        
        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login'); //unauthorized !  test passes because of middleware auth
        
        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);   //forbidden
    }
    
    /** @test */
    function authorized_users_can_delete_replies()
    {
        //$user = create('App\User', ['name' => 'Fiso']);
        //$this->signIn($user); //testing authservieprovider gate before
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
        $this->delete("/replies/{$reply->id}")->assertStatus(302); //->assertRedirect('/');        
        
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count); //fresh cause must get a fresh copy of thread after the reply delete
    }

    /** @test */
    function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();
        
        $reply = create('App\Reply');
        
        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');
        
        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }
    
    /** @test */
    function authorized_users_can_update_replies()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);
  
        $updatedReply = 'You been changed, fool.';
        $this->patch("/replies/{$reply->id}", ['body' => $updatedReply]);
        
        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updatedReply]);
    }
    
    /** @test */
    function replies_that_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling(); //302 (HTTP FOUND, redirects back to previous page and passes through the errors)
            //before the 302, we intercept (app\Exception\Handler.php) the "ValidationException: The given data was invalid." 
        
        $this->signIn();
        $thread = create('App\Thread');
        
        $reply = make('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);
        
        //$this->expectException(\Exception::class); 
        //$this->post($thread->path().'/replies', $reply->toArray());

        ////with try/catch :
        //$this->post($thread->path().'/replies', $reply->toArray())
        $this->json('post', $thread->path().'/replies', $reply->toArray())
            ->assertStatus(422);
    }
    
    /** @test */
    function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->withExceptionHandling(); //Expected status code 422 but received 403. (lui 429, too many requests, au lieu de 422)
        
        $this->signIn();
        $thread = create('App\Thread');        
        
        $reply = make('App\Reply', [
            'body' => 'My simple reply'
        ]);

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(200);

        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertStatus(429);
    }
}
