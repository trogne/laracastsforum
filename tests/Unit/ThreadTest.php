<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;
    
    protected $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();
    }
    
    /** @test */
    function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread'); // need to persist it, since no ID if make
        $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->id}", $thread->path()
        );
    }    
    
    /** @test */
    function a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    function a_thread_has_replies()
    {
        //$reply = factory('App\Reply')->create(['thread_id' => $thread->id]);
        
        //$this->assertInstanceOf('App\Reply', $thread->replies[0]);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }
    
    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);
        
        $this->assertCount(1, $this->thread->replies);
    }
    
    /** @test */
    function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');
        
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }
    
    /** @test */
    function a_thread_can_be_subscribed_to()
    {
        $thread = create('App\Thread');
        
        //$this->signIn();
        
        //$user->subscribeToThread();
        //$user->subscriptions; //on thread instead
        $thread->subscribe($userId = 1);     // $email->send();
        
        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }
    
    /** @test */
    function a_thread_can_be_unsubscribed_from()
    {
        $thread = create('App\Thread');

        $thread->subscribe($userId = 1);
        
        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }
    
    /** @test */
    function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();
        
        $this->assertTrue($thread->isSubscribedTo);
    }
}