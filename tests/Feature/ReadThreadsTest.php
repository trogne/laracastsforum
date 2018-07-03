<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadsTest extends TestCase
{
    use RefreshDatabase; //encompasses either situations : use DatabaseMigrations ET use DatabaseTransactions

    protected $thread;

    public function setUp()  //overriding setUp
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();
    }
    
    /** @test */
    function a_user_can_view_all_threads()
    {
        //$response = $this->get('/threads');
        //$response->assertStatus(200);
        //$response->assertSee($this->thread->title);
        $this->get('/threads')
            //->assertStatus(200)
            ->assertSee($this->thread->title);        
    }

    /** @test */
    function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path()) //primary key (id) instead of a slug
            ->assertSee($this->thread->title);
    }
    
    ///** @test */   //deletd car comme an_authenticated_user_may_participate_in_forum_threads
    //function a_user_can_read_replies_that_are_associated_with_a_thread()
    //{
    //    $reply = factory('App\Reply')
    //            ->create(['thread_id' => $this->thread->id]);
    //    
    //    $this->get($this->thread->path())
    //        ->assertSee($reply->body);
    //}

    /** @test */
    function a_user_can_read_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        //$this->assertTrue($channel->threads->contains($threadInChannel)); // see ChannelTest.php
        
        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }
    
    /** @test */
    function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' => 'JohnDoe']));
        
        //var_dump(app(\Illuminate\Contracts\Auth\Factory::class)->id());
        //dd(auth()->id());
        //dd(\Auth::user()->name); 
        
        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');
        
        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }
    
    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);
        
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);
        
        $threadWithNoReplies = $this->thread;
        
        $response = $this->getJson('threads?popular=1')->json();

        //dd(array_column($response, 'replies_count')); //dd(array_keys($response[0]));
        $this->assertEquals([3,2,0], array_column($response, 'replies_count'));
    }

    /** @test */
    function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        //unanswered thread created at setup
        $thread = create('App\Thread'); 
        create('App\Reply', ['thread_id' => $thread->id]);
        
        $response = $this->getJson('threads?unanswered=1')->json();
        
        $this->assertCount(1, $response);
    }
    
    /** @test */
    function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id], 2);
        
        //$response = $this->get($thread->path() . '/replies');
        $response = $this->getJson($thread->path() . '/replies')->json(); //ou get

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
