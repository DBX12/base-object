<?php


namespace dbx12\baseObject\exceptions;

use Exception;

class UnknownPropertyException extends Exception
{
    public static function forSettingProperty(object $object, string $propertyName): UnknownPropertyException
    {
        $className = get_class($object);
        return new self("Setting unknown property $className::$propertyName");
    }

    public static function forGettingProperty(object $object, string $propertyName): UnknownPropertyException
    {
        $className = get_class($object);
        return new self("Getting unknown property $className::$propertyName");
    }
}
