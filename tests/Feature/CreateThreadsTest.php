<?php
//namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; //use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Activity;
use App\Thread;

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
            ->assertRedirect(route('login'));            

        //$this->post('/threads')
        $this->post(route('threads'))
            ->assertRedirect(route('login'));
    }
    
    /** @test */
    function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();
        
        //$this->withExceptionHandling()->signIn($user);
        $this->signIn($user);
        
        $thread = make('App\Thread');
        
        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email address.');
    }
    
    /** @test */
    function a_user_can_create_new_form_threads()
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
            //->assertstatus(422); //if using try/catch, now not needed cause  if ($request->expectsJson()) in Handler,  donc pas catching... bubble up...
    }
    
    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body'); //marche pu car je capte le ValidationException dans app/Exception/Handler.php
            //->assertstatus(422); //if using try/catch, now not needed cause  if ($request->expectsJson()) in Handler,  donc pas catching... bubble up...
            
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel', 2)->create();
        
        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');
            //->assertstatus(422); //if using try/catch, now not needed cause  if ($request->expectsJson()) in Handler,  donc pas catching... bubble up...
             
        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
            //->assertstatus(422); //if using try/catch, now not needed cause  if ($request->expectsJson()) in Handler,  donc pas catching... bubble up...
            
    }
    
    /** @test */
    function a_thread_requires_a_unique_slug()
    {
        $this->signIn();
        
        //create('App\Thread', [], 2); //to offset the beginning id
        
        //$thread = create('App\Thread', ['title' => 'Foo Title', 'slug' => 'foo-title']);
        $thread = create('App\Thread', ['title' => 'Foo Title']); //the model event "created" changes the Factory slug
        
        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        //$this->post(route('threads'), $thread->toArray());
        //$this->assertTrue(Thread::whereSlug('foo-title-2')->exists());

        //$this->post(route('threads'), $thread->toArray());
        //$this->assertTrue(Thread::whereSlug('foo-title-3')->exists());

        $thread = $this->postJson(route('threads'), $thread->toArray())->json(); //->status();
        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }
    
    /** @test */
    function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();
        
        //$thread = create('App\Thread', ['title' => 'Some Title 24', 'slug' => 'some-title-24']);
        $thread = create('App\Thread', ['title' => 'Some Title 24']);
        
        //$this->post(route('threads'), $thread->toArray());
        //$this->assertTrue(Thread::whereSlug('some-title-24-2')->exists());
        $thread = $this->postJson(route('threads'), $thread->toArray())->json();
        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);        
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
        
        return $this->post(route('threads'), $thread->toArray());
    }
}
