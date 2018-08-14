<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function it_has_an_owner()
    {
        $reply = factory('App\Reply')->create();
        
        $this->assertInstanceOf('App\User', $reply->owner);
    }
    
    /** @test */
    function it_knows_if_it_was_just_publisehd()
    {
        $reply = create('App\Reply');
        
        $this->assertTrue($reply->wasJustPublished());
        
        $reply->created_at = Carbon::now()->subMonth();
        
        $this->assertFalse($reply->wasJustPublished());
    }
    
    /** @test */
    function it_can_detect_all_mentioned_users_in_the_body()
    {
        //$reply = create('App\Reply', [
        //    'body' => '@JaneDoe wantes to talk to @JohnDoe'
        //]);
        $reply = new \App\Reply([
            'body' => '@JaneDoe wantes to talk to @JohnDoe'
        ]);        
        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }
    
    /** @test */
    function it_wraps_mentioned_usernames_in_the_body_within_anchor_tags()
    {
        //$reply = create('App\Reply', [
        //    'body' => 'Hello @Jane-Doe.'
        //]);
        $reply = new \App\Reply([
            'body' => 'Hello @Jane-Doe.'
        ]);
        
        $this->assertEquals(
            'Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.',
            $reply->body
        );
    }
    
    /** @test */
    function it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');
        
        $this->assertFalse($reply->isBest());
        
        $reply->thread->update(['best_reply_id' => $reply->id]);
        
        $this->assertTrue($reply->fresh()->isBest());
    }

    /** @test */
    function a_reply_body_is_sanitized_automatically()
    {
        $reply = make('App\Reply', ['body' => '<script>alert("gotcha")</script><p>This is okay.</p>']);
        
        $this->assertEquals('<p>This is okay.</p>', $reply->body);
    }
}
