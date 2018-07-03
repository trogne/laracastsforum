<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PorfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_a_profile(){
        $user = create('App\User');
        
        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }
    
    /** @test */
    function profiles_display_all_threads_created_by_the_associated_user()
    {
        //$user = create('App\User');
        ////$this->be($user); // $this-actingAs($user);
        ////\Auth::login($user); // \Auth::loginUsingId($user->id);
        //$thread = create('App\Thread', ['user_id' => $user->id]);
        
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        
        //$this->get("/profiles/{$user->name}")
        $this->get("/profiles/" . auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
