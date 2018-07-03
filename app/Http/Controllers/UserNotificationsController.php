<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserNotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //return auth()->user()->unreadNotifications()->get();
        return auth()->user()->unreadNotifications;
    }
    
    public function destroy(User $user, $notificationId)
    {
        //dd(count((new \ReflectionClass(get_class($user->notifications()->find($notificationId))))->getMethods())); //237
        //foreach(((new \ReflectionClass(get_class($user->notifications()->find($notificationId))))->getMethods()) as $name) {
        //    echo $name->name . ' ';
        //}
        //die();
        //dd((new \ReflectionClass(get_class($user->notifications()->find($notificationId))))->getShortName()); //"DatabaseNotification"

        //$user->notifications()->find($notificationId)->markAsRead();
        auth()->user()->notifications()->findOrFail($notificationId)->markAsRead(); // auth() cause always assume that delete your own notification
    }
}
