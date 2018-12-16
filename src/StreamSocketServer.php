<?php

namespace Concurrency;

class StreamSocketServer
{
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function serveForever($host, $port)
    {
        $handleClient = $this->handler;
        $sock = new StreamSocket(
            stream_socket_server("tcp://{$host}:{$port}")
        );
        while (true) {
            $client = yield from $sock->accept();
            Loop::newTask($handleClient($client));
        }
    }
}
