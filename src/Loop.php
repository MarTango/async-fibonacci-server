<?php
namespace Concurrency;

/**
 * Basic event loop
 */
class Loop
{
    private static $lastId = 0;
    private static $tasks = [];
    private static $waiting = [];
    private static $recvWait = [];
    private static $sendWait = [];

    public static function newTask($task)
    {
        $task->id = self::$lastId++;
        $task->started = false;
        self::$tasks[] = $task;
    }

    /**
     * Use stream_select to see if any sockets are ready for reading (and
     * writing).
     */
    private static function waitForIO()
    {
        list($readSocks, $sendSocks) = [self::$recvWait, self::$sendWait];

        $ready = stream_select($readSocks, $sendSocks, $e, 0);

        foreach ($readSocks as $taskId => $sock) {
            self::$tasks[] = self::$waiting[$taskId];
            unset(self::$recvWait[$taskId]);
            unset(self::$waiting[$taskId]);
        }

        foreach ($sendSocks as $taskId => $sock) {
            self::$tasks[] = self::$waiting[$taskId];
            unset(self::$sendWait[$taskId]);
            unset(self::$waiting[$taskId]);
        }
    }

    private static function handleNextTask()
    {
        $task = array_shift(self::$tasks);
        if (!$task->valid()) {
            echo "Task done ", $task->id, PHP_EOL;
            return;
        }

        if ($task->started) {
            $task->next(); // Go to next yield
            list($why, $sock) = $task->current();
        } else {
            list($why, $sock) = $task->current();
            $task->started = true;
        }

        self::$waiting[$task->id] = $task;

        switch ($why) {
            case "recv":
                self::$recvWait[$task->id] = $sock;
                break;
            case "send":
                self::$sendWait[$task->id] = $sock;
                break;
        }
    }

    public static function run($task)
    {
        self::newTask($task);

        while (self::$tasks || self::$recvWait || self::$sendWait) {
            if (!self::$tasks) {
                self::waitForIO();
            } else {
                self::handleNextTask();
            }
        }
    }
}
