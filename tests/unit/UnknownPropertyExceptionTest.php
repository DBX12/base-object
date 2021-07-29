<?php

namespace tests\unit;

use Codeception\Test\Unit;
use dbx12\baseObject\exceptions\UnknownPropertyException;
use tests\support\dummies\BaseObjectDummy;

/**
 * Class UnknownPropertyExceptionTest
 *
 * @package tests\unit
 * @coversDefaultClass \dbx12\baseObject\exceptions\UnknownPropertyException
 */
class UnknownPropertyExceptionTest extends Unit
{

    /**
     * @covers ::forSettingProperty
     */
    public function testForSettingProperty(): void
    {
        $object          = new BaseObjectDummy();
        $propertyName    = 'theProperty';
        $className       = get_class($object);
        $exception       = UnknownPropertyException::forSettingProperty($object, $propertyName);
        $expectedMessage = "Setting unknown property $className::$propertyName";
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }

    /**
     * @covers ::forGettingProperty
     */
    public function testForGettingProperty(): void
    {
        $object          = new BaseObjectDummy();
        $propertyName    = 'theProperty';
        $className       = get_class($object);
        $exception       = UnknownPropertyException::forGettingProperty($object, $propertyName);
        $expectedMessage = "Getting unknown property $className::$propertyName";
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
