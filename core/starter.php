<?php

// load functions
require_once(__DIR__."/functions.php");

// add autolad for classes
spl_autoload_register(function($className) {
    $classPath = realpath(__DIR__."/../class/");
    if ($classPath) {
        $tabClassFile = glob("$classPath/$className.php");
        $classFile  = $tabClassFile[0] ?? "";
        if ($classFile) {
            require_once($classFile);
        }
    }  

});

