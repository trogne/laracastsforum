<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Trending;
//use Illuminate\Support\Facades\Redis;

class TrendingTest extends TestCase
{
    use RefreshDatabase;
    
    protected $trending;

    public function setUp()
    {
        parent::setUp();
        
        $this->trending = new Trending();
        
        //Redis::del('testing_trending_threads');
        $this->trending->reset();
    }
    
    /** @test */
    function it_stores_trending_threads_in_redis()
    {
        //$trending = new Trending();
        
        //$this->assertEmpty($trending->get());
        $this->assertEmpty($this->trending->get());
        
        //$trending->push(factory('App\Thread')->make());
        //$this->trending->push(factory('App\Thread')->make());
        $this->trending->push(new FakeThread('Boring Thread'));
        
        $this->trending->push(new FakeThread('Popular Thread'));
        $this->trending->push(new FakeThread('Popular Thread')); //redis will increment the score
        $this->trending->push(new FakeThread('Popular Thread'));
        
        //$this->assertCount(1, $trending->get());
        $this->assertCount(2, $trending = $this->trending->get());

        //$this->assertEquals('Popular Thread', $this->trending->get()[0]->title);
        $this->assertEquals(['Popular Thread', 'Boring Thread'], array_pluck($trending, 'title')); //array_pluck = laravel helper
    }
}

class FakeThread
{
    public $title;
    
    public function __construct($title)
    {
        $this->title = $title;
    }
    
    public function path()
    {
        return 'some/path';
    }
}
