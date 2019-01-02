<?php declare(strict_types = 1);

namespace App;

use App\Library\Monitor;
use App\Controller\PilgrimController;
use App\Library\Logger\FileLogger;
use App\Service\Storage\StorageService;
use App\ValueObject\File;
use App\Service\Storage\FilestorageService;
use App\Service\ScraperService;
use App\ValueObject\Path;
use App\Repository\PilgrimRepository;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use WebSocket;
use App\Library\Config;

class Factory
{

    public function createPilgrimController() : PilgrimController
    {
        return new PilgrimController(
            $this->createFileLogger(),
            $this->createScraperService(),
            $this->createPilgrimRepository(),
            $this->createWebSocketClient(),
            $this->createConfig()
        );
    }

    public function createFileLogger(): FileLogger
    {
        return new FileLogger(File::createFromString(__DIR__ . '/../tmp/error.log'));
    }

    public function createFileStorageService(): StorageService
    {
        return new FilestorageService(Path::createFromString(__DIR__ . '/../fileStorage/'), $this->createFileLogger());
    }

    public function createScraperService(): ScraperService
    {
        return new ScraperService($this->createFileLogger());
    }

    public function createPilgrimRepository(): PilgrimRepository
    {
        return new PilgrimRepository($this->createFileStorageService());
    }

    public function createMonitor() : Monitor
    {
        return new Monitor();
    }

    public function createWebSocketClient() : WebSocket\Client
    {
        $config = $this->createConfig();
        
        return new WebSocket\Client('ws://localhost:' . $config->get('monitor.websocket_port'));
    }

    public function createWebSocketServer() : IoServer
    {
        $config = $this->createConfig();
        
        return IoServer::factory(
            new HttpServer(new WsServer($this->createMonitor())),
            $config->get('monitor.websocket_port')
        );
    }

    public function createConfig() : Config
    {
        return new Config(File::createFromString(__DIR__ . '/../config/config.ini'));
    }
}
