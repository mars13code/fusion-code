<?php

trait TraitCore
{
    // properties
    public static $tabData = [];

    // methods
    public static function set ($key, $value)
    {
        self::$tabData[$key] = $value;
    }

    public static function get ($key, $default="")
    {
        return self::$tabData[$key] ?? $default;
    }

    public static function out ($key, $default="")
    {
        echo self::$tabData[$key] ?? $default;
    }

    public static function getOne ($className)
    {
        return self::$tabData[$className] ?? self::$tabData[$className] = new $className;
    }

    // magic methods
    public static function __callStatic($className, $tabArgument)
    {
        $obj = self::getOne($className);
        $method = $tabArgument[0] ?? "";
        $tabParam = array_slice($tabArgument, 1);
        // http://php.net/manual/fr/function.method-exists.php
        if (method_exists($obj, $method))
        {
            $obj->$method($tabParam);
        }
        
        return $obj;
    }
    
}