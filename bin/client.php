<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$connector = new \React\Socket\Connector($loop);

$connector->connect('127.0.0.1:8787')->then(function (\React\Socket\ConnectionInterface $server) use ($loop) {
    $server->pipe(new \React\Stream\WritableResourceStream(STDOUT, $loop));


    $command = [
        'command' => 'close_connection',
        'collection_name' => 'test_table',
        'documents' => [
            [
                '_id' => '00Abcd-7876xg-os',
                'name' => 'Client'
            ]
        ]
    ];

    $server->write(json_encode($command, JSON_THROW_ON_ERROR, 512));
});

$loop->run();
