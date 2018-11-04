<?php
declare(strict_types = 1);
namespace App\Cli;

use App\Factory;
require_once (__DIR__ . '/../../vendor/autoload.php');

define('MAX_RUNTIME_SECONDS', 60);

$factory = new Factory();
$startTime = time();
do {
    $pilgrim = $factory->createPilgrim();
    $pilgrim->run();
} while (time() - $startTime < MAX_RUNTIME_SECONDS);
 

