<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;

class RegisterConfirmationController extends Controller
{
    public function index()
    {
        ////dd(request('token')); //ou dd(request()->get('token')); //dd(request->all());        
        //if(request('token') == auth()->user()->confirmation_token) {
        //    //auth()->user()->update([
        //    //    'confirmed' => !auth()->user()->confirmed
        //    //]);   //marche pas car mass-assignment exception
        //    auth()->user()->confirmed = true;
        //    auth()->user()->save();
        //}
        
        //User::where('confirmation_token', request('token'))
        //    ->firstOrFail()
        //    ->update(['confirmed' => true]); //marche pas car mass-assignment exception (needs 'confirmed' in fillable, or an empty guarded array)

        //$user = User::where('confirmation_token', request('token'))
        //    ->firstOrFail();
        //$user->confirmed = true;
        //$user->save();

        //try {
        //    User::where('confirmation_token', request('token')) //request()->get('token')
        //        ->firstOrFail()
        //        //->update(['confirmed' => true]);
        //        ->confirm(); //method on User, updates confirmed to true            
        //} catch (\Exception $e){
        //    return redirect(route('threads'))
        //        ->with('flash', 'Unknown token.');
        //}
        
        $user = User::where('confirmation_token', request('token'))->first();
            
        if(!$user) {
            return redirect(route('threads'))->with('flash', 'Unknown token.');            
        }
        
        $user->confirm();
        
        return redirect(route('threads'))
            ->with('flash', 'Your account is now confirmed! You may post to the forum.');
    }
}
