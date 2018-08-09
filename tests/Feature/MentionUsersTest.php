<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase; // use DatabaseMigrations (au lieu de DatabaseTransactions)
    
    /** @test */
    function mentioned_users_in_a_reply_are_notified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        $jane = create('App\User', ['name' => 'JaneDoe']);
        
        $thread = create('App\Thread');
        
        $reply = make('App\Reply', [ //raw ?
            'body' => '@JaneDoe look at this.'
        ]);
        
        $this->json('post', $thread->path().'/replies', $reply->toArray()); //signal that we want a json response
        
        $this->assertCount(1, $jane->notifications);
    }
    
    /** @test */
    function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create('App\User', ['name' => 'johndoe']);
        create('App\User', ['name' => 'johndoe2']);
        create('App\User', ['name' => 'janedoe']);
        
        $results = $this->json('GET', '/api/users', ['name' => 'john']);
        
        //$this->assertEquals(['johndoe', 'johndoe2'], $results->json());
        $this->assertCount(2, $results->json());
    }    
}
