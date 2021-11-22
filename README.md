# Base object

[![Build](https://github.com/DBX12/base-object/actions/workflows/php.yml/badge.svg)](https://github.com/DBX12/base-object/actions/workflows/php.yml)

This library is heavily inspired by the configurable objects of the [Yii2 framework](https://www.yiiframework.com/)

## Installation

As with any composer library:

`composer require dbx12/base-object`

## Concept

The base object allows you to set the properties of a class with a configuration array. By default, only **public and
protected** properties can be set this way. If you want to set a private property, define a setter for it. The pattern
for setter is `set` + property name, e.g. for the property `$name` -> `function setName($value)`. The getter names are
created similar (for the same example: `function getName()`).

If you expose your private or protected properties with public setters and getters, you can help your IDE by annotating
your class with `@property-read` and `@property-write` annotations. If you have a getter and a setter, you can combine
them into `@property`. For above example (without the setter), you would write `@property-write string $name`.

## Usage

**Default (Without setter)**

```php
class MyObject extends \dbx12\baseObject\BaseObject {
    public $publicVariable;
    protected $protectedVariable;
    private $privateVariable;
}

// this will fail with an UnknownPropertyException because setting $privateVariable is not allowed
$instance = new MyObject([
    'publicVariable' => 'publicValue',
    'protectedVariable' => 'protectedValue',
    'privateVariable' => 'privateValue',
]);
```

**With protected setter**

```php
class MyObject extends \dbx12\baseObject\BaseObject {
    public $publicVariable;
    protected $protectedVariable;
    private $privateVariable;
    
    protected function setPrivateVariable($value): void
    {
        $this->privateVariable = $value;
    }
}

// this will succeed
$instance = new MyObject([
    'publicVariable' => 'publicValue',
    'protectedVariable' => 'protectedValue',
    'privateVariable' => 'privateValue',
]);

// and this will produce an error as the setter is not visible from the global scope
$myObject->setPrivateVariable('bar');
```

**Without a public getter**

```php
class MyObject extends \dbx12\baseObject\BaseObject {
    public $publicVariable;
    protected $protectedVariable;
    private $privateVariable;

    protected function setPrivateVariable($value): void
    {
        $this->privateVariable = $value;
    }
}

$myObject = new MyObject([
    'publicVariable' => 'publicValue',
    'protectedVariable' => 'protectedValue',
    'privateVariable' => 'privateValue',
]);

// this will throw an UnknownPropertyException
echo $myObject->protectedVariable;
```

**With a public getter**

```php
/**
 * @property-read $protectedVariable
 */
class MyObject extends \dbx12\baseObject\BaseObject {
    public $publicVariable;
    protected $protectedVariable;
    private $privateVariable;

    protected function setPrivateVariable($value): void
    {
        $this->privateVariable = $value;
    }

    public function getProtectedVariable()
    {
        return $this->protectedVariable;
    }
}

$myObject = new MyObject([
    'publicVariable' => 'publicValue',
    'protectedVariable' => 'protectedValue',
    'privateVariable' => 'privateValue',
]);

// this will succeed
echo $myObject->getProtectedVariable();

// this will succeed and your IDE will show you a hint for it thanks to the @property-read annotation
echo $myObject->protectedVariable;
```
