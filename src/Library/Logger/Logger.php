<?php
namespace App\Library\Logger;

interface Logger
{ 
    public function warning (string $message);
    
    public function error(string $message);
}

