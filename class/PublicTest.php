<?php

// file:        PublicTest.php
// creation:    2018-06-20 20:21:27
// licence:     MIT
// author:      mars13.fr

class PublicTest
{
    // TRAITS
    use TraitOne;
    
    // PROPERTIES
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        
    }

    function message ()
    {
        return date("H:i:s");
    }
    
    //@end
    
}