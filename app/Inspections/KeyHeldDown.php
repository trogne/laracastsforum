<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown
{
    public function detect($body)
    {
        if(preg_match('/(.)\\1{4,}/', $body, $matches) == true) {
            throw new Exception('keystrokes...');
        }        
    }    
}

// \\1 = previous grouping, donc même charactère repris 4 fois
