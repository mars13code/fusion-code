<?php

// file:        TraitOne.php
// creation:    2018-06-04 11:07:37
// licence:     MIT
// author:      mars13.fr

trait TraitOne
{
    // PROPERTIES
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        
    }

    // http://php.net/manual/fr/language.oop5.magic.php#object.invoke
    public function __get ( string $name )
    {
        return $this;
    }
    
    public function __invoke($param)
    {
        if ($param) echo json_encode($param);
        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this($arguments);    
    }
    
    public static function __callStatic($name, $arguments)
    {
        static $obj = null;
        if ($obj == null)
            $obj = new self; 
        return $obj($arguments);    
    }
    
}