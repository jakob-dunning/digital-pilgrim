<?php
declare(strict_types = 1);
namespace App\Library\Logger;

interface Logger
{

    public function warning(string $message);

    public function error(string $message);
}

