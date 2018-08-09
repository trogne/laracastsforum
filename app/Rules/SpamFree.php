<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Inspections\Spam;

class SpamFree implements Rule
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
    public function passes($attribute, $value) //$attribute = name of the input from the form
    {
        try {
            return ! resolve(Spam::class)->detect($value);            
        } catch (\Exception $e){
            //throw new \Exception('miko de maki'); //server side... exception handling... exception page with miko de maki
            return false;
        }        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() //5.4: resources\lang\en\validation.php
    {
        return 'The :attribute contains spam';
    }
}
