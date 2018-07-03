<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('/login');
    }
    
    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();
        
        $reply = create('App\Reply'); //also creates a thread (and a user) in the process
        
        $this->post('replies/' . $reply->id . '/favorites');
        
        //$this->assertDatabaseHas('users', ['email' => 'sally@example.com']);
        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_any_reply()
    {
        $this->signIn();
        
        $reply = create('App\Reply');
        
        //$this->post('replies/' . $reply->id . '/favorites');
        //$this->assertCount(1, $reply->favorites);

        $reply->favorite(); //use the api directly
        
        $this->delete('replies/' . $reply->id . '/favorites');

        //$this->assertCount(0, $reply->fresh()->favorites);
        $this->assertCount(0, $reply->favorites);
    }
    
    /** @test */
    function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        //$this->withExceptionHandling(); //sinon: $this->expectException(\Illuminate\Database\QueryException::class);
        $this->signIn();
         
        $reply = create('App\Reply');
        
        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e){
            $this->fail('Did not expect to insert the same record set twice.');
        }
                
        //dd(\App\Favorite::all()->toArray());
        $this->assertCount(1, $reply->favorites);        
    }
}
