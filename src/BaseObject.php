<?php
/** @noinspection PhpUnused */


namespace dbx12\baseObject;


use dbx12\baseObject\exceptions\InvalidCallException;
use dbx12\baseObject\exceptions\UnknownPropertyException;

/**
 * Class BaseObject
 * Inspired by https://github.com/yiisoft/yii2/blob/master/framework/base/BaseObject.php
 *
 * @package dbx12\baseObject
 */
abstract class BaseObject
{

    public function __construct($config=[]){
        if(!empty($config)){
            $this->configure($config);
        }
        $this->init();
    }

    /**
     * Configures the object with initial parameters. Every array key must correspond to a property of the object.
     * @param array $config
     */
    protected function configure(array $config): void
    {
        foreach($config as $key => $value){
            $this->$key = $value;
        }
    }

    /**
     * Called after the object was created and configured.
     * @codeCoverageIgnore as this method is empty
     */
    protected function init(): void
    {
    }

    /**
     * @throws \dbx12\baseObject\exceptions\UnknownPropertyException
     */
    public function __get($name)
    {
        $getterName = 'get' . ucfirst($name);
        if (method_exists($this, $getterName)) {
            return $this->$getterName();
        }
        if (method_exists($this, 'set' . ucfirst($name))) {
            throw new InvalidCallException('Getting write-only property ' . get_class($this) . '::' . $name);
        }
        throw UnknownPropertyException::forGettingProperty($this, $name);
    }

    /**
     * @throws \dbx12\baseObject\exceptions\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        $setterName = 'set' . ucfirst($name);
        if (method_exists($this, $setterName)) {
            $this->$setterName($value);
            return;
        }
        if (method_exists($this, 'get' . ucfirst($name))) {
            throw new InvalidCallException('Setting read-only property ' . get_class($this) . '::' . $name);
        }
        throw UnknownPropertyException::forSettingProperty($this, $name);
    }

    public function __isset($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        return false;
    }

    public function __unset($name)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter(null);
            return;
        }
        if (method_exists($this, 'get' . ucfirst($name))) {
            throw new InvalidCallException('Unsetting read-only property ' . get_class($this) . '::' . $name);
        }
    }
}
