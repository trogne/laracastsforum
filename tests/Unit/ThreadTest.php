<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ThreadWasUpdated;
//use Illuminate\Support\Facades\Redis;

class ThreadTest extends TestCase
{
    use RefreshDatabase;
    
    protected $thread;

    public function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();
        //$this->thread = create('App\Thread');
    }
    
    /** @test */
    function a_thread_has_a_path()
    {
        $thread = create('App\Thread'); // need to persist it, since no ID if make
        $this->assertEquals(
            //"/threads/{$thread->channel->slug}/{$thread->id}", $thread->path()
            "/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path()
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
    function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake(); //static::swap(new NotificationFake); //replaces the underlying instance of the facade with a fake version
        
        //$this->signIn();
        //
        //$this->thread->subscribe();
        //
        //$this->thread->addReply([
        //    'body' => 'Foobar',
        //    'user_id' => 1
        //]);

        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'Foobar',
                'user_id' => 999 //or create another user
            ]);
        
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class); //regardless of the channel //not saving to db, not sending email, all being stored locally
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
    
    /** @test */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();
        
        $thread = create('App\Thread');

        tap(auth()->user(), function ($user) use ($thread) {   // ou \Auth::user()
            $this->assertTrue($thread->hasUpdatesFor($user));
    
            //$key = sprintf('users.%s.visits.%s', auth()->id(), $thread->id);
            //cache()->forever(
            //    $user->visitedThreadCacheKey($thread),
            //    \Carbon\Carbon::now()
            //);
            $user->read($thread);
            
            $this->assertFalse($thread->hasUpdatesFor($user));            
        });
    }
    
    ///** @test */
    //function a_thread_records_each_visit()
    //{
    //    $thread = make('App\Thread', ['id' => 1]); //make is not persisting to db, so no ID
    //    
    //    //Redis::del("threads.{$thread->id}.visits");
    //    //$thread->resetVisits();
    //    $thread->visits()->reset($thread);        
    //
    //    //$this->assertSame(0, $thread->visits()); //car avec assertEquals, 0 = null
    //    $this->assertSame(0, $thread->visits()->count());
    //    
    //    //$thread->recordVisit();
    //    $thread->visits()->record($thread);        
    //    
    //    //$this->assertEquals(1, $thread->visits());
    //    $this->assertSame(1, $thread->visits()->count());
    //    
    //    //$thread->recordVisit();
    //    $thread->visits()->record($thread);       
    //    
    //    //$this->assertEquals(2, $thread->visits());
    //    $this->assertSame(2, $thread->visits()->count());
    //}
    
    ///** @test */
    //function a_thread_may_be_locked()
    //{
    //    $this->assertFalse($this->thread->locked);
    //    
    //    $this->thread->lock();
    //    
    //    $this->assertTrue($this->thread->locked);
    //}
    
    /** @test */
    function a_thread_body_is_sanitized_automatically()
    {
        $thread = make('App\Thread', ['body' => '<script>alert("gotcha")</script><p>This is okay.</p>']);
        
        $this->assertEquals('<p>This is okay.</p>', $thread->body);
    }
}
