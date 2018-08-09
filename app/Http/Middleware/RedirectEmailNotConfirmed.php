<?php

namespace App\Http\Middleware;

use Closure;

class RedirectEmailNotConfirmed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //if(!auth()->user()->confirmed) {
        if(!$request->user()->confirmed) { //can get the user off of the request
            //return redirect('/threads')->with('flash', json_encode([
            //    'message' => 'You must first confirm your email address.',
            //    'level' => 'danger'                
            //]));
            return redirect('/threads')->with('flash', 'You must first confirm your email address.');
        }
        
        return $next($request);
    }
}
