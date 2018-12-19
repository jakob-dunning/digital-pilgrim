<?php
declare(strict_types = 1);
namespace App\Cli;

use App\Factory;
require_once (__DIR__ . '/../vendor/autoload.php');

define('MAX_RUNTIME_SECONDS', 600);

$factory = new Factory();
$pilgrimController = $factory->createPilgrimController();
$startTime = time();

do {
    $pilgrimController->run();
} while (time() - $startTime < MAX_RUNTIME_SECONDS);
 

