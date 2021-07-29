<?php


namespace tests\support\dummies;


/**
 * Class BaseObjectDummy
 *
 * @package dbx12\baseObject\tests\dummies
 * @property string $annotatedProperty
 */
class BaseObjectDummy extends \dbx12\baseObject\BaseObject
{
    public $publicProperty;
    protected $protectedProperty;
    private $privateProperty;
    private $privatePropertyWithGetter;
    private $privatePropertyWithSetter;

    protected $readOnly;
    protected $writeOnly;

    protected $internal;

    public function setDefaults(): void
    {
        $this->publicProperty            = 'public';
        $this->protectedProperty         = 'protected';
        $this->privateProperty           = 'private';
        $this->privatePropertyWithGetter = 'private';
        $this->privatePropertyWithSetter = 'private';
        $this->internal['annotated']     = 'annotated';
        $this->readOnly                  = 'readOnly';
        $this->writeOnly                 = 'writeOnly';
    }

    public function setAnnotatedProperty($value): void
    {
        $this->internal['annotated'] = $value;
    }

    public function getAnnotatedProperty()
    {
        return $this->internal['annotated'] ?? null;
    }

    public function getPrivatePropertyWithGetter()
    {
        return $this->privatePropertyWithGetter;
    }

    public function setPrivatePropertyWithSetter($value): void
    {
        $this->privatePropertyWithSetter = $value;
    }

    public function getReadOnly()
    {
        return $this->readOnly;
    }

    public function setWriteOnly($value): void
    {
        $this->writeOnly = $value;
    }
}
