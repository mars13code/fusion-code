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
        return false;
    }
    //@end
    
}