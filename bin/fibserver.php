<?php
namespace Concurrency;

require_once "vendor/autoload.php";

function fib (int $n): int {
    return ($n < 2) ? 1 : fib($n - 1) + fib($n - 2);
};

$clientHandler = function (StreamSocket $client): \Generator {
    echo "Client connected\n";
    while (true) {
        $data = yield from $client->read(100);
        if (!$data) {
            break;
        }
        $result = fib((int)$data);
        yield from $client->write((string)$result . "\n");
    }
    echo "Client disconnected\n";
};

$main = function () use ($clientHandler): \Generator {
    $server = new StreamSocketServer($clientHandler);
    yield from $server->serveForever("localhost", 25000);
};

// Start the eventLoop
Loop::run(
    $main()
);
