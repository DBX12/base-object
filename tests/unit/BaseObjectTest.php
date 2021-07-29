<?php

namespace tests\unit;

use Codeception\AssertThrows;
use Codeception\Specify;
use Codeception\Test\Unit;
use dbx12\baseObject\exceptions\InvalidCallException;
use dbx12\baseObject\exceptions\UnknownPropertyException;
use tests\support\dummies\BaseObjectDummy;
use tests\support\Helper\ReflectionHelpers;
use tests\UnitTester;

/**
 * Class BaseObjectTest
 *
 * @package tests\unit
 * @coversDefaultClass \dbx12\baseObject\BaseObject
 */
class BaseObjectTest extends Unit
{
    use ReflectionHelpers;
    use AssertThrows;
    use Specify;

    /** @var UnitTester */
    protected $tester;

    /**
     * @covers ::__get
     * @throws \Throwable
     */
    public function test__get(): void
    {
        $dummy = new BaseObjectDummy();
        $dummy->setDefaults();

        $this->specify('The object')
            ->should('allow access to a public property', function () use ($dummy) {
                $this->tester->assertEquals('public', $dummy->publicProperty);
            })
            ->should('allow access to properties with a getter', function () use ($dummy) {
                $this->tester->assertEquals('annotated', $dummy->annotatedProperty);
            })
            ->shouldNot('allow access to a protected property without a getter', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    UnknownPropertyException::class,
                    'Getting unknown property ' . BaseObjectDummy::class . '::protectedProperty',
                    function () use ($dummy) {
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $value = $dummy->protectedProperty;
                    }
                );

            })
            ->shouldNot('allow access to a private property without a getter', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    UnknownPropertyException::class,
                    'Getting unknown property ' . BaseObjectDummy::class . '::privateProperty',
                    function () use ($dummy) {
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $value = $dummy->privateProperty;
                    }
                );
            })
            ->should('allow access to a private property with a getter', function () use ($dummy) {
                $this->tester->assertEquals('private', $dummy->privatePropertyWithGetter);
            })
            ->shouldNot('allow access to a write-only property', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    InvalidCallException::class,
                    'Getting write-only property ' . BaseObjectDummy::class . '::writeOnly',
                    function () use ($dummy) {
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $value = $dummy->writeOnly;
                    }
                );
            });
    }

    /**
     * @covers ::__set
     */
    public function test__set(): void
    {
        $dummy = new BaseObjectDummy();

        $this->specify('The object')
            ->should('allow access to a public property', function () use ($dummy) {
                $dummy->publicProperty = 'set-public';
                $actual                = $this->getInaccessibleProperty($dummy, 'publicProperty');
                $this->assertEquals('set-public', $actual);
            })
            ->should('allow access to properties with a setter', function () use ($dummy) {
                $dummy->annotatedProperty = 'set-annotated';
                $actual                   = $this->getInaccessibleProperty($dummy, 'internal')['annotated'];
                $this->assertEquals('set-annotated', $actual);
            })
            ->shouldNot('allow access to a protected property without a setter', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    UnknownPropertyException::class,
                    'Setting unknown property ' . BaseObjectDummy::class . '::protectedProperty',
                    function () use ($dummy) {
                        $dummy->protectedProperty = 'set-protected';
                    }
                );
            })
            ->shouldNot('allow access to a private property without a setter', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    UnknownPropertyException::class,
                    'Setting unknown property ' . BaseObjectDummy::class . '::privateProperty',
                    function () use ($dummy) {
                        $dummy->privateProperty = 'set-private';
                    }
                );
            })
            ->should('allow access to a private property with a setter', function () use ($dummy) {
                $dummy->privatePropertyWithSetter = 'set-private';
                $actual                           = $this->getInaccessibleProperty($dummy, 'privatePropertyWithSetter');
                $this->assertEquals('set-private', $actual);
            })
            ->shouldNot('allow access to a read-only property', function () use ($dummy) {
                $this->assertThrowsWithMessage(
                    InvalidCallException::class,
                    'Setting read-only property ' . BaseObjectDummy::class . '::readOnly',
                    function () use ($dummy) {
                        $dummy->readOnly = 'set-readonly';
                    }
                );
            });
    }

    /**
     * @covers ::__isset
     */
    public function test__isset(): void
    {
        $dummy = new BaseObjectDummy();

        $this->specify('The method __isset()')
            ->should('return false for unknown properties', function () use ($dummy) {
                $this->assertFalse(isset($dummy->unknownProperty));
            })
            ->should('return false for empty properties', function () use ($dummy) {
                $this->assertFalse(isset($dummy->annotatedProperty));
            });

        $dummy->setDefaults();
        $this->specify('The method __isset()')
            ->should('return false for unknown properties', function () use ($dummy) {
                $this->assertFalse(isset($dummy->unknownProperty));
            })
            ->should('return true for set properties', function () use ($dummy) {
                $this->assertTrue(isset($dummy->annotatedProperty));
            });
    }

    /**
     * @covers ::__unset
     */
    public function test__unset(): void
    {
        $dummy = new BaseObjectDummy();

        $this->specify('The method __unset()')
            ->should('unset writable properties', function () use ($dummy) {
                $dummy->setDefaults();
                $this->assertNotEmpty($dummy->annotatedProperty, 'Ensure default is set');
                unset($dummy->annotatedProperty);
                $this->assertEmpty($dummy->annotatedProperty);
            })
            ->shouldNot('unset read-only properties', function () use ($dummy) {
                $dummy->setDefaults();
                $this->assertNotEmpty($dummy->readOnly, 'Ensure default is set');
                $this->assertThrowsWithMessage(
                    InvalidCallException::class,
                    'Unsetting read-only property ' . BaseObjectDummy::class . '::readOnly',
                    function () use ($dummy) {
                        unset($dummy->readOnly);
                    }
                );
                $this->assertNotEmpty($dummy->readOnly);
            });
    }

    /**
     * @covers ::configure
     */
    public function testConfigure(): void
    {
        $dummy = new BaseObjectDummy();

        $this->specify('Method configure()')
            ->should('set writable properties', function () use ($dummy) {
                $data = [
                    'publicProperty'    => random_int(PHP_INT_MIN, PHP_INT_MAX),
                    'annotatedProperty' => random_int(PHP_INT_MIN, PHP_INT_MAX),
                ];
                $this->invokeMethod($dummy, 'configure', [$data]);
                $this->assertEquals($data['publicProperty'], $dummy->publicProperty);
                $this->assertEquals($data['annotatedProperty'], $dummy->annotatedProperty);
            })
            ->shouldNot('set private properties without setter', function () use ($dummy) {
                $data = [
                    'privatePropertyWithGetter' => random_int(PHP_INT_MIN, PHP_INT_MAX),
                ];
                $this->assertThrowsWithMessage(
                    InvalidCallException::class,
                    'Setting read-only property ' . BaseObjectDummy::class . '::privatePropertyWithGetter',
                    function () use ($dummy, $data) {
                        $this->invokeMethod($dummy, 'configure', [$data]);
                    }
                );
            })
            ->should('set private properties with setter', function () use ($dummy) {
                $data = [
                    'privatePropertyWithSetter' => random_int(PHP_INT_MIN, PHP_INT_MAX),
                ];
                $this->invokeMethod($dummy, 'configure', [$data]);
                $actual = $this->getInaccessibleProperty($dummy, 'privatePropertyWithSetter');
                $this->assertEquals($data['privatePropertyWithSetter'], $actual);
            });
    }

    /**
     * @covers ::__construct
     */
    public function test__construct(): void
    {
        $this->specify('Constructor')
            ->should('call configure and init if config given', function () {

                $mock = $this->getMockBuilder(BaseObjectDummy::class)
                    ->onlyMethods(['init', 'configure'])
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
                $mock->expects($this->once())->method('init');
                $mock->expects($this->once())->method('configure');

                $config = [
                    'foo' => 'bar',
                ];

                $this->invokeMethod($mock, '__construct', [$config]);
            })
            ->should('call only init if no config given', function () {

                $mock = $this->getMockBuilder(BaseObjectDummy::class)
                    ->onlyMethods(['init', 'configure'])
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
                $mock->expects($this->once())->method('init');
                $mock->expects($this->never())->method('configure');
                $config = [];
                $this->invokeMethod($mock, '__construct', [$config]);
            });
    }
}
