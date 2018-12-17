# PHP Basic Concurrency 

A simple implementation of an event loop.

Influenced by David Beazley's PyCon 2015 Talk, [Concurrency From the
Ground Up](https://www.youtube.com/watch?v=MCs5OvhV9S4) and Nikic's
blog post [Cooperative multitasking using
coroutines](https://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html)

## Basic Usage
 - `composer dump-autoload`

### Start the server

```bash
php bin/fibserver.php
```

### Connect to the server

```bash
nc localhost 25000
```

