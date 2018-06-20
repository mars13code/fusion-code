<?php

// file:        Explorer.php
// creation:    2018-06-08 09:54:27
// licence:     MIT
// author:      mars13.fr

class Explorer
{
    // TRAITS
    use TraitOne;
    
    // PROPERTIES
    public $tabData = [];
    
    // METHODS
    
    // constructor
    function __construct ()
    {
        
    }
    
    function buildList ($tabSearchDir)
    {
        $rootDir0 = Core::get("rootdir0");
        
        $searchDir0     = $rootDir0;
        $count          = 0;
        foreach($tabSearchDir as $searchDir)
        {
            $level      = count(explode("/", $searchDir)) -1;
            $tabFile = glob($rootDir0.$searchDir."/*");
            if (!empty($tabFile))
            {
                // print_r($tabFile);
                foreach($tabFile as $index => $file)
                {
                    $title = str_replace($searchDir0, "", $file);
                    // http://php.net/manual/fr/function.pathinfo.php
                    $extension = ""; // debug: reset as dir don't have extension
                    extract(pathinfo($title));
                    if (is_dir($file))
                    {
                        $this->tabData["$title/"] = [ "level" => $level, "id" => 1 + $count , "isdir" => "D", "dirname" => $dirname, "basename" => $basename, "filename" => $filename, "extension" => $extension ?? "", "title" => $title];
                    }
                    else
                    {
                        $this->tabData[$title] = [ "level" => $level, "id" => 1 + $count , "isdir" => "F", "dirname" => $dirname, "basename" => $basename, "filename" => $filename, "extension" => $extension ?? "", "title" => $title];
                    }
                    $count++;
                }
            }  
        }
        
        // http://php.net/manual/fr/function.sort.php
        ksort($this->tabData, SORT_NATURAL);        
        
        $this->tabData = array_values($this->tabData);

        return $this;
    }

}