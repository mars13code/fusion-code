<?php

require_once(__DIR__."/../core/starter.php");

// new Core(__DIR__);
Core::Core()
    ->setPath([dirname(__DIR__)])
    ->loadCode()
    ->runCode()
    ;