<?php

// file:        Request.php
// creation:    2018-06-20 18:47:30
// licence:     MIT
// author:      mars13.fr

class Request
{
    // TRAITS
    use TraitOne;
    
    // PROPERTIES
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        
    }
    
    function isAjax ()
    {
        $result = false;

        $uri = $_SERVER["REQUEST_URI"];
        // http://php.net/manual/fr/function.parse-url.php
        $tabUrl = parse_url($uri);
        extract($tabUrl);
        // http://php.net/manual/fr/function.pathinfo.php
        $tabPath = pathinfo($path ?? "");
        extract($tabPath);
        // fixme:
        if ($filename == "ajax") $result = true;

        return $result;
    }
    //@end
    
}