<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase; //our tests is migrating the database, so it creates the activities tale...
    
    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();
        
        $thread = create('App\Thread');
        
        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);
        
        $activity = Activity::first();
        
        $this->assertEquals($activity->subject->id, $thread->id);
    }
    
    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();
        
        $reply = create('App\Reply');
        
        $this->assertEquals(2, Activity::count());
    }
    
    /** @test */
    function it_fetches_a_feed_for_any_user()
    {
        //dd( \Carbon\Carbon::now()->subWeek());
        $this->signIn();
        
        //create('App\Thread', ['user_id' => auth()->id()]);
        //
        //create('App\Thread', [
        //    'user_id' => auth()->id(),
        //    'created_at' => Carbon::now()->subWeek() //thread date, not activity date!
        //]);

        create('App\Thread', ['user_id' => auth()->id()], 2);

        //auth()->user()->activity()->first()->update(['created_at' => (new Carbon)->now()->subWeek()]);
        //auth()->user()->activity()->first()->update(['created_at' => (new Carbon)->subWeek()]);
        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);
        
        //$feed = (new Activity)->feed(auth()->user());
        //$feed = (new Activity);
        //$feed->max = 'miko';
        //$feed = $feed::feed(auth()->user());
        $feed = Activity::feed(auth()->user());
        
        //dd($feed->keys());
        //dd($feed->toArray());
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
