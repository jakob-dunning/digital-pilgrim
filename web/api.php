<?php
declare(strict_types = 1);

use App\Factory;
require_once (__DIR__ . '/../vendor/autoload.php');

$factory = new Factory();
$monitorController = $factory->createMonitorController();

header('Content-Type: application/json');
echo $monitorController->getTemplateVars();

