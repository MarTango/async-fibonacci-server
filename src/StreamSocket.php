<?php

namespace Concurrency;

class StreamSocket
{
    private $sock;

    public function __construct($sock)
    {
        $this->sock = $sock;
    }

    public function read($maxSize)
    {
        yield ["recv", $this->sock];
        return fread($this->sock, $maxSize);
    }

    public function write($data)
    {
        yield ["send", $this->sock];
        return fwrite($this->sock, $data);
    }

    public function accept()
    {
        yield ["recv", $this->sock];
        return new self(stream_socket_accept($this->sock));
    }
}
