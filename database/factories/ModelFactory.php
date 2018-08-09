<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

//dd(get_class($factory)); //Illuminate\Database\Eloquent\Factory

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'confirmed' => true
    ];
});

$factory->state(App\User::class, 'unconfirmed', function () {
    return [
        'confirmed' => false
    ];
});

$factory->state(App\User::class, 'administrator', function () {
    return [
        'name' => 'JohnDoe' //ou 'is_admin' collumn :   'is_admin' => true
    ];
});

$factory->define(App\Thread::class, function (Faker $faker) {
    
    $title = $faker->sentence;
    
    return [
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'channel_id' => function(){
            return factory('App\Channel')->create()->id;
        },
        //'title' => $faker->sentence,
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => 0,
        'slug' => str_slug($title),
        'locked' => false
    ];
});

$factory->define(App\Channel::class, function ($faker) {
    $name = $faker->word;
    
    return [
        'name' => $name,
        'slug' => $name
    ];
});

$factory->define(App\Reply::class, function (Faker $faker) {
    return [
        'thread_id' => function(){
            return factory('App\Thread')->create()->id;
        },
        'user_id' => function(){
            //return Auth::user()->id ?: factory('App\User')->create()->id; //ou auth()->id() // auth()->user()->id;
            return factory('App\User')->create()->id;
        },
        'body' => $faker->paragraph,
    ];
});

$factory->define(\Illuminate\Notifications\DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function () {
            return auth()->id() ?: factory('App\User')->create()->id;
        },
        'notifiable_type' => 'App\User',
        'data' => ['foo' => 'bar']
    ];
});
