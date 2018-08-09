<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trending;

class TrendingThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        
        $this->trending = new \App\Trending();
        
        //Redis::del($this->trending->cacheKey());
        $this->trending->reset();
    }
    
    /** @test */
    function it_increments_a_thread_score_each_time_it_is_read()
    {
        //////redis is a facade, and there are some very useful test-specific helpers that allow us to swap out the underlying instance
        //////take the trending class that is in the laravel container, and replace it with a fake trending, and then that can just be sort of like an in-memory data store that you use (not depend/rely upon redis for tests)
        //app()->instance(Trending::class, new FakeTrending); //tell laravel, the instance that you have within the container, swap that out with a new instance of FakeTrending
        //$trending = app(Trending::class); //ou resolve(Trending::class);
        ////dd(get_class($trending)); //"Tests\Feature\FakeTrending"
        //$trending->assertEmpty();
        //$thread = create('App\Thread');
        //$this->call('GET', $thread->path());
        //$trending->assertCount(1);
        ////$this->assertEquals($thread->title, json_decode($trending->threads[0])->title);
        //$this->assertEquals($thread->title, $trending->threads[0]->title); //lol, pas besoin de json_decode App/Thread

        //////below using Redis instead of FakeTrending :
        //$this->assertEmpty(Redis::zrevrange('testing_trending_threads', 0, -1));
        $this->assertEmpty($this->trending->get());
        
        $thread = create('App\Thread');
        
        //$this->get($thread->path()); //works too
        $this->call('GET', $thread->path());
        
        //$d = Redis::zrevrange('trending_threads', 0, -1, 'WITHSCORES');
        //$d = Redis::zrevrange('trending_threads', 0, -1);
        
        //$trending = Redis::zrevrange('trending_threads', 0, -1);
        //$trending = $this->trending->get();
        
        $this->assertCount(1, $trending = $this->trending->get());
        
        //$this->assertEquals($thread->title, json_decode($trending[0])->title);
        $this->assertEquals($thread->title, $trending[0]->title);
    }
}

class FakeTrending extends \App\Trending
{
    public $threads = [];
    
    public function push($thread) //overwrite the push method
    {
        $this->threads[] = $thread;
    }
    
    public function assertEmpty()
    {
        \PHPUnit\Framework\Assert::assertEmpty($this->threads);
    }
    
    public function assertCount($count)
    {
        \PHPUnit\Framework\Assert::assertCount($count, $this->threads);
    }    
}
