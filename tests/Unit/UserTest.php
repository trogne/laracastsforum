<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ThreadWasUpdated;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\User');
        $reply = create('App\Reply', ['user_id' => $user->id]);
        
        $this->assertEquals($reply->id, $user->lastReply->id);
    }
    
    /** @test */
    function a_user_can_determine_their_avatar_path()
    {
        //$user = create('App\User', ['avatar_path' =>'avatars/me.jpg']);
        $user = create('App\User');
        
        //$this->assertEquals('http://forum.app/avatars/default.jpg', $user->avatar());
        $this->assertEquals(asset('images/avatars/default.png'), $user->avatar_path);
        
        $user->avatar_path = 'avatars/me.jpg';
        
        //$this->assertEquals('http://forum.app/avatars/me.jpg', $user->avatar());
        $this->assertEquals(asset('avatars/me.jpg'), $user->avatar_path);
    }
}
