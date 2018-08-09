<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    function it_checks_for_invalid_keywords()
    {
        //$this->expectException(\Exception::class);

        $spam = new Spam();
        
        $this->assertFalse($spam->detect('Innocent reply here'));
        
        $this->expectException('Exception'); //ou \Exception::class
        
        $this->assertFalse($spam->detect('yahoo customer support'));
        
    }
    
    /** @test */
    function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception'); //ou \Exception::class

        $spam->detect('Hello world aaaaaaaaa');
    }
}
