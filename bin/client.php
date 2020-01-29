<?php

use Morebec\YDB\Client\ClientConfiguration;
use Morebec\YDB\Client\Client;

require __DIR__ . '/../vendor/autoload.php';

$config = new ClientConfiguration();
$client = new Client($config);
$client->connect();
$client->createCollection('remote_collection');


