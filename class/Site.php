<?php

class Site
{
    // traits
    use TraitCore;

    // constructor
    function __construct (...$tabParam)
    {
        //echo "(Site)";
        // echo date("H:i:s");
        Core::Core()
            ->setPath([ dirname(__DIR__) ])   // fixme
            ->loadCode()
            ->runCode()
            ;
    }
}