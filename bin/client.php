<?php

use Morebec\YDB\Client\ClientHandler;
use Morebec\YDB\YDBInMemoryClient;
use Morebec\YDB\YDBInMemoryClientConfiguration;

require __DIR__ . '/../vendor/autoload.php';

$config = new YDBInMemoryClientConfiguration();
$client = new YDBInMemoryClient($config);
$client->connect();
$client->createCollection('remote_collection');


