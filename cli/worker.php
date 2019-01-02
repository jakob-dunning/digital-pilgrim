<?php
declare(strict_types = 1);
namespace App\Cli;

use App\Factory;
require_once (__DIR__ . '/../vendor/autoload.php');

$factory = new Factory();
$pilgrimController = $factory->createPilgrimController();
$config = $factory->createConfig();

$startTime = time();

do {
    $pilgrimController->run();
    sleep((int) $config->get('pilgrim.seconds_wait_between_executions'));
} while (time() - $startTime < (int) $config->get('pilgrim.max_runtime_seconds'));
 

