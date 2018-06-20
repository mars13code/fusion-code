<?php

require_once(__DIR__."/../core/starter.php");


// set rootdir for Router
global $cmsDir;
global $rootDir;
// global $rootDir0;

//$rootDir0   = realpath(__DIR__ . "/../../");    // full path
$rootDir0   = realpath(__DIR__ . "/");    // full path
$cmsDir     = "$rootDir0/fusion-code";               // full path
$rootdir    = str_replace($_SERVER["DOCUMENT_ROOT"], "", realpath(__DIR__ . "/../")); // relative path

// new Core(__DIR__);
Core::Core()
    // files
    ->setVar("rootdir",     $rootdir)   
    ->setVar("rootdir0",    $rootDir0)   
    ->setVar("cmsDir",      $cmsDir)   
    // core
    ->setPath([dirname(__DIR__)])
    ->addCode(__DIR__."/../core/core-theme.php", "500-core-theme")    // add special core file
    ->loadCode()
    ->runCode()
    ;