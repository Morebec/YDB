<?php

use Morebec\YDB\InMemory\InMemoryServer;
use Morebec\YDB\InMemory\InMemoryServerConfig;
use Morebec\YDB\Server\ServerHandler;

require __DIR__ . '/../vendor/autoload.php';


$config = new InMemoryServerConfig();
$server = new InMemoryServer($config);
$serverHandler = new ServerHandler($server);

$serverHandler->start();
