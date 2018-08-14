<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Zttp\Zttp;

class Recaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //if (app()->runningUnitTests()) return true;
        
        $response = Zttp::asFormParams()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $value,  //no longer have to fetch it off of the request cause already linked to that key (g-recaptcha-response) // $request->input('g-recaptcha-response')
            'remoteip' => request()->ip() //au lieu de $_SERVER['REMOTE_ADDR']
        ]);
        
        //if (! $response->json()['success']) {
        //    throw new \Exception('Recaptcha failed');
        //}
        return $response->json()['success'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        //return 'The validation error message.';
        //return 'The :attribute is invalid gniochon!!!!';
        return 'The recaptcha verification failed. Try again.';
    }
}
