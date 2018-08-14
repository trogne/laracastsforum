<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;

class SearchTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);
        
        $search = 'foobar';
        
        create('App\Thread', [], 2);
        //$desiredThreads = create('App\Thread', ['body' => "A thread with the {$search} term."], 2);
        create('App\Thread', ['body' => "A thread with the {$search} term."], 2);
        
        //$results = $this->getJson("/threads/search?q={$search} term.")->json();
        do {
            sleep(.25);
            $results = $this->getJson("/threads/search?q={$search} term.")->json()['data'];
        } while (empty($results));
        
        $this->assertCount(2, $results); //get data from the paginated object

        //$desiredThreads->unsearchable(); //can call from Illuminate\Database\Eloquent\Collection  //remove from algolia indices  //  class Collection use trait Macroable... Searchable trait :  bootSearchable() ... (new static)->registerSeachableMacros()
        Thread::latest()->take(4)->unsearchable(); //call from Illuminate\Database\Eloquent\Builder"
    }
}
