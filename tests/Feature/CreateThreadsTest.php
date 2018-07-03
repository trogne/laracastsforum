<?php
//namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; //use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Activity;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase; //use DatabaseMigrations;
    
    ///** @test */
    //function guests_may_not_create_threads()
    //{
    //    $this->expectException('Illuminate\Auth\AuthenticationException');
    //    //$thread = factory('App\Thread')->make();
    //    $thread = make('App\Thread');
    //    $this->post('/threads', $thread->toArray());
    //}
    //
    ///** @test */
    //function guests_cannot_see_the_create_thread_page()
    //{
    //    $this->withExceptionHandling()->get('/threads/create')
    //        ->assertRedirect('/login');
    //}
    /** @test */
    function guests_may_not_create_threads()
    {
        //$this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withExceptionHandling(); // Exception not thrown
        
        $this->get('/threads/create')
            ->assertRedirect('/login');            

        $this->post('/threads')
            ->assertRedirect('/login');
    }
    
    /** @test */
    function an_authenticated_user_can_create_new_form_threads()
    {
        //$this->be(factory('App\User')->create());
        //$this->actingAs(factory('App\User')->create()); //same, butreturns $this (Tests\Feature\CreateThreadsTest)
        //$this->actingAs(create('App\User'));
        $this->signIn();
        
        //$thread = factory('App\Thread')->raw(); //raw array of values, NOT A THREAD INSTANCE
        //$thread = factory('App\Thread')->make();
        //$thread = create('App\Thread');
        $thread = make('App\Thread');
        
        //$this->post('/threads', $thread->toArray());
        $response = $this->post('/threads', $thread->toArray());
        //$this->get($thread->path())
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
            //->assertSee((string)$thread->channel_id);
    }
    
    /** @test */
    function a_thread_requires_a_title()
    {
        ////$this->expectException('Illuminate\Database\QueryException');
        ////$this->expectException('Illuminate\Validation\ValidationException'); //with validation in ThreadsController
        //
        ////$this->signIn();
        //////let's turn on ExceptionHandling (so that laravel can catch the exception, like in prod, and throw an errors variable into the session
        //$this->withExceptionHandling()->signIn();
        //
        //$thread = make('App\Thread', ['title' => null]);
        //
        //$this->post('/threads', $thread->toArray())
        //    ->assertSessionHasErrors('title'); //...it's gonna throw a validation exception, and it's going to throw an errors variable into the session 
        
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }
    
    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();
        
        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');
             
        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');            
    }
    
    /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        //$this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        //$response = $this->json('DELETE', $thread->path()); //non sinon 401 (unauthorized) //$response->assertStatus(401);
        $response = $this->delete($thread->path())->assertRedirect('/login');
        
        $this->signIn();
        $response = $this->delete($thread->path())->assertStatus(403);
        
    }
    
    /** @test */
    function authorized_users_cans_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());
        
        $response->assertStatus(204); //200 by default
        
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]); //vs assertDatabaseHas
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        //$this->assertDatabaseMissing('activities', [
        //    'subject_id' => $thread->id,
        //    'subject_type' => get_class($thread)
        //]);
        //$this->assertDatabaseMissing('activities', [
        //    'subject_id' => $reply->id,
        //    'subject_type' => get_class($reply)
        //]);
        
        $this->assertEquals(0, Activity::count());        
    }
    
    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        
        $thread = make('App\Thread', $overrides);
        
        return $this->post('/threads', $thread->toArray());
    }
}
