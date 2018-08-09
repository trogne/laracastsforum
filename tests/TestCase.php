<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler; //interface, NOT THIS : use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        DB::statement('PRAGMA foreign_keys=on');        

        $this->disableExceptionHandling();
    }
    
    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');
        
        $this->actingAs($user); //or $this->be($user);
        
        return $this;
    }
    
    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        //dd(get_class($this->oldExceptionHandler)); // "App\Exceptions\Handler"

        //updates the ExceptionHandler class within the container
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {} //override the construct of Illuminate\Foundation\Exceptions\Handler (ce construct est aussi exécuté car extends before)
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }
    
    protected function withExceptionHandling()
    {
        //$this->app->instance(ExceptionHandler::class, new class extends Handler {
        //    public function __construct() {}
        //});
        //$this->app->instance(ExceptionHandler::class, new Handler($this->app));
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }
}
