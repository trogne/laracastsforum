<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use App\Exceptions\ThrottleException;
//use App\Exceptions\ThreadIsLockedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    //public function render($request, Exception $exception)
    //{
    //    if (app()->environment() === 'testing') throw $exception;
    //    return parent::render($request, $exception);
    //}

    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            if ($request->expectsJson()) {
                //return response('Sorry, validation failed.', 422);
                //return response($exception->getMessage(), 422);
                $errors = '';
                //foreach($e->errors() as $key => $value) { //errors method returns 'messages' = instance of MessageBag
                //    $errors .= $value[0];
                //};
                foreach(collect($exception->errors()) as $error) {
                    $errors .= $error[0];
                };
                return response($errors, 422);
            }
        }

        //if ($exception instanceof ThreadIsLockedException) {
        //    //dd($exception->getMessage());
        //    return response( 'Thread Is Locked', 422);
        //}
        
        if ($exception instanceof ThrottleException) {
            return response( 'You are replying too frequently.', 429);
        }
        //else {
        //    dd(get_class($exception)); // "Illuminate\Auth\AuthorizationException", "Illuminate\Auth\AuthenticationException"
            return parent::render($request, $exception); // render an exception that is bubbled all the way up to the top
        //}
    }    
}
