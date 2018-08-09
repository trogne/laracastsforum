<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Channel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //var_dump('boot'); //after register
        //\View::composer(['threads.create', 'threads.index'], function ($view){
        //\View::share('channels', Channel::all()); //run before our database migrations run (RefreshDatabase)
        \View::composer('*', function ($view){  //this query won't trigger until the view is loaded
            $channels = \Cache::rememberForever('channelsdemiko', function (){
                return Channel::all(); //remembered on second view load //fully fetch only if not already in the cache, and then later if you want to throw this into a repository... or you could create a dedicated View composer class and then reference a repository dependency. VOIR https://laravel.com/docs/5.6/views
            });
            //$view->with('channels', Channel::all());
            $view->with('channels', $channels);
        });
        
        //\Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
        //5.5 : php artisan make:rule SpamFree (auto scaffold class within App\Rules dir)
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //var_dump('register'); //before boot
        //\View::composer('*', function ($view){
        //    $view->with('channels', Channel::all());
        //});
        //\View::share('channels', Channel::all()); //NOT WORKING HERE
        //dd(env('APP_ENV'));
        //dd(\App::environment());
        ///////FOR LARAVEL 5.4 and below : (5.5: auto-discovery, so for below to work, need "dont-discover" in composer.json
        //if ($this->app->runningInConsole()) {
        //if ($this->app->environment('local', 'testing')) {      
        if($this->app->isLocal()){
            //$this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
            //$this->commands(App\Console\Commands\DuskCommand::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
