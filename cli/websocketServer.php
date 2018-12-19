<?php
declare(strict_types = 1);
namespace App\Cli;

use App\Factory;
require_once (__DIR__ . '/../vendor/autoload.php');

$factory = new Factory();
$server = $factory->createWebSocketServer();

$server->run();


