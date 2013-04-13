<?php

namespace Basecamp;

class DummyLogger
{
    function __call($name, $arguments)
    {
        $allowedNames = array('debug', 'info', 'warn', 'error', 'fatal');
        $name = strtolower($name);

        if (!in_array($name, $allowedNames)) {
            error_log("$name is not a method of DummyLogger");
            return;
        }

        if (count($arguments) !== 1) {
            error_log("not exactly one argument to DummyLogger method");
            return;
        }

        $this->_log($name, $arguments[0]);
    }

    private function _log($level, $message)
    {
        error_log("[$level] $message");
    }
}