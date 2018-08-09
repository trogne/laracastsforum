<?php

namespace App\Http\Middleware;

use Closure;

class Administrator
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
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);   //proceed with next layer of the onion
        }
        
        //throw
        //return redirect(route('threads'));
        //return response('You do not have permission to lock this thread.', 403);
        abort(403, 'You do not have permission to perform this action.');
    }
}
