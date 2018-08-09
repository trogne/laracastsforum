<?php

namespace App\Inspections;

use Exception;

//class InvalidKeywords implements Mako //make that explicit in an interface that each of these implements detect
class InvalidKeywords
{
    protected $keywords = [
        'yahoo customer support'        
    ]; 
    
    //public static function detect($body)
    public function detect($body)
    {
        foreach($this->keywords as $keyword) {
            if(stripos($body, $keyword) !==false) {
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}