<?php

// CLASS AUTOLOAD

if (!function_exists('loadClass'))
{

    function loadClass (...$tabParam)
    {
        // list of dir to search
        static $tabDir = [];
        if (empty($tabDir))
        {
            $tabDir["100"] = realpath(__DIR__ . "/../");    
        }
        
        if (count($tabParam) == 2)
        {
            $key    = $tabParam[0] ?? "";
            $value  = $tabParam[1] ?? "";
            if (is_dir(realpath($value)))
            {
                // add folder to the search list
                $tabDir["$key"] = $value;
            }
        }
        
        if (count($tabParam) == 1)
        {
            $className = $tabParam[0];
            $classFile = "";
            $classDir  = __DIR__ . "/../";
            foreach($tabDir as $classDir)
            {
                $tabClass = glob("$classDir/class/$className.php");
                $classFile = $tabClass[0] ?? "";
                if ($classFile)
                {
                    require_once($classFile);
                    // stop searching
                    break;
                }
            }
            if ($classFile == "")
            {
                // auto create code from Demo.php
                $classDir  = __DIR__ . "/../class";
                $demoClass = "$classDir/Demo.php";
                if (is_file($demoClass))
                {
                    $codePHP = file_get_contents($demoClass);
                    $codePHP = str_replace("Demo", $className, $codePHP);
                    $codePHP = str_replace("DATE_CREATION", date("Y-m-d H:i:s"), $codePHP);
                    
                    $classFile = "$classDir/$className.php";
                    file_put_contents($classFile, $codePHP);
                    
                    require_once($classFile);
                }
            }

        }
    }
    
    spl_autoload_register("loadClass");
}