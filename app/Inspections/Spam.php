<?php

namespace App\Inspections;

use App\Inspections\InvalidKeywords;
use App\Inspections\KeyHeldDown;

class Spam  //kind of like a manager class (just lists all of its detectors or inspection classes and then trigger them one by one)
{
    protected $inspections = [   //could store those in a config file or a service provider and then inject into class
        InvalidKeywords::class,
        KeyHeldDown::class
    ];
    
    public function detect($body)
    {
        //$this->detectInvalidKeywords($body);
        //$this->detectKeyHeldDown($body);
        
        foreach($this->inspections as $inspection) {
            //(new $inspection)->detect($body);
            app($inspection)->detect($body); //with laravel container instead (create an instance, or fetch one out of the container... resolve them out of the container)
        }

        return false;
    }
    
    //protected function detectInvalidKeywords($body)
    //{
    //    //$invalidKeywords = [
    //    //    'yahoo customer support'
    //    //];
    //    //
    //    //foreach($invalidKeywords as $keyword) {
    //    //    if(stripos($body, $keyword) !==false) {
    //    //        throw new \Exception('Your reply contains spam.');
    //    //    }
    //    //}
    //    //InvalidKeywords::detect($body);
    //    (new InvalidKeywords)->detect($body);
    //}
    //
    //protected function detectKeyHeldDown($body)
    //{
    //    //if(preg_match('/(.)\\1{4,}/', $body, $matches) == true) {
    //    //    throw new \Exception('Your reply contains spam.');
    //    //}
    //    (new KeyHeldDown)->detect($body);
    //}
}
