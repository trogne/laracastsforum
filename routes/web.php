<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//use App\Http\Middleware\RedirectEmailNotConfirmed;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::view('scan', 'scan');

Route::get('/threads', 'ThreadsController@index')->name('threads');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/search', 'SearchController@show');

//Route::get('/threads/{channel}', 'ChannelsController@index');
//Route::get('/threads/{thread}', 'ThreadsController@show');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update');

Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy');
//Route::post('/threads', 'ThreadsController@store')->middleware(RedirectEmailNotConfirmed::class);
Route::post('/threads', 'ThreadsController@store')->middleware('must-be-confirmed'); //middleware defined in app/Http/Kernel
Route::get('/threads/{channel}', 'ThreadsController@index'); //and check if we have a channel, and if we do, get the threads associated with channel, if not, we just  get all

//Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update')->name('threads.update');
Route::post('locked-threads/{thread}', 'LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');
Route::delete('locked-threads/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.destroy')->middleware('admin');

//Route::resource('threads', 'ThreadsController');
//Route::post('/threads/{thread}/replies', 'RepliesController@store'); //->name('addReply');
Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
//Route::middleware('throttle:1')->post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::patch('/replies/{reply}', 'RepliesController@update');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::post('/threads/{channel}/{thread}/subscriptions',  'ThreadSubscriptionsController@store')->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions',  'ThreadSubscriptionsController@destroy')->middleware('auth');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');
//Route::get('/profiles/{user}', 'ProfilesController@show'); //and check if we have a channel, and if we do, get the threads associated with channel, if not, we just  get all
Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');

Route::get('api/users', 'Api\UsersController@index');
Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->middleware('auth')->name('avatar');

Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');

//dedicated api controller... api endpoint : /api/threads/1/replies


//Route::resource('user', 'AdminUserController')->parameters([
//    'user' => 'admin_user'
//]);
