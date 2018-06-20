<?php

// file:        Test.php
// creation:    2018-06-20 20:12:54
// licence:     MIT
// author:      mars13.fr

class Test
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