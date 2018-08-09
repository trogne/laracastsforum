<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserAvatarController extends Controller
{
    public function store()
    {
        //dd(file_get_contents("php://input"));
        //dd('AOSIDJASOIDJASOIDASD');
        //dd(request()->file());
        //dd($_SERVER['HTTP_X_FILE_NAME']); //si set avec setRequestHeader
        
        //dd(request('avatar')->path());
        //return back()->withErrors(['first error', 'second error']); // Errors = reserved, becomes a $errors var in blade, NOT session('errors')
        //return back()->withMessages(['まだ作ってないよ！', 'miko']);
        //return back()->with('messages', ['まだ作ってないよ！', 'miko']);
        //return back()->withInput(); // Input = reserved, like Errors
        
        //$this->validate(request(),[]) //5.4
        request()->validate([
            'avatar' => ['required', 'image']
        ]);
        
        //request()->file('avatar')->store('avatars', 'public'); //returns path as hash: request()->file('avatar')->hashName() 
        auth()->user()->update([
            //'avatar_path' => request()->file('avatar')->storeAs('avatars', $hash, 'public')
            //'avatar_path' => request('avatar')->store('avatars', 'public') //works too
            'avatar_path' => request()->file('avatar')->store('avatars', 'public')  //avatar = file upload input name;   avatars folder, public driver/disk
        ]);
        
        //return back();
        return response([], 204);
    }
}

//stored here:
//default : \storage\app\public\avatars
//testing : \storage\framework\testing\disks\public\avatars


