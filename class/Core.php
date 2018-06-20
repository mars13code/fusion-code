<?php

// file:        Core.php
// creation:    2018-06-20 17:46:09
// licence:     MIT
// author:      mars13.fr

class Core
{
    // TRAITS
    use TraitCore;
    
    // PROPERTIES
    public $tabPath        = [];
    public $tabFramework   = [];
    public $tabCodeFile    = [];
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        self::$tabData["Core"] = $this; // auto singleton ?!

        echo "(Core)";
    }
 
    function setPath ($tabPath)
    {
        //print_r($tabPath);
        $this->tabPath = $tabPath;
        foreach($tabPath as $curPath)
        {
            $frameworkPath = realpath("$curPath/core");
            if (is_dir($frameworkPath)) 
                $this->tabFramework[] = $frameworkPath;
        }
        //print_r($this->tabFramework);
        // chain syntax
        return $this;
    }

    function loadCode ()
    {
        // print_r($this->tabFramework);
        foreach($this->tabFramework as $fPath)
        {
            $tabFile = glob("$fPath/[0-9]*.php");
            foreach($tabFile as $curFile)
            {
                extract(pathinfo($curFile));
                $this->tabCodeFile["$filename"] = $curFile; 
            }
        }

        ksort($this->tabCodeFile, SORT_NATURAL);

        // chain syntax
        return $this;
    }

    function runCode ()
    {
        // print_r($this->tabCodeFile);

        foreach($this->tabCodeFile as $codeFile)
        {
            // should be unique ?
            require_once($codeFile);
        }

        // chain syntax
        return $this;        
    }

    //@end
    
}