<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuscribeToThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_subcribe_to_threads()
    {
        $this->signIn();
        
        $thread = create('App\Thread');
        
        $this->post($thread->path() . '/subscriptions');
        
        $this->assertCount(1, $thread->subscriptions); //pas besoin fresh() ici
    }
    
    /** @test */
    public function a_user_can_unsubcribe_from_threads()
    {
        $this->signIn();
        
        $thread = create('App\Thread');
        
        $thread->subscribe();
        
        $this->delete($thread->path() . '/subscriptions');
        
        $this->assertCount(0, $thread->subscriptions);
    }
}
