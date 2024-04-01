<?php

namespace App\Enums;
use App\Exceptions\EnumException;

/**
 * Class BaseEnums
 *
 * @package App\Enums
 */
abstract class BaseEnum implements \JsonSerializable
{
    /**
     * @var array
     */
    private static $_classInstances = [];

    /**
     * @var array
     */
    private static $_reflectClasses = [];

    /**
     * @var string
     */
    private $_name;

    /**
     * @var mixed
     */
    private $_value;

    /**
     * BaseEnums constructor.
     *
     * @param string $name  enum key
     * @param mixed  $value enum value
     */
    public function __construct($name, $value)
    {
        $this->_name = $name;
        $this->_value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return array
     */
    public static function getKeys()
    {
        return array_keys(self::toArray());
    }

    /**
     * @return array
     */
    public static function getValues()
    {
        return array_values(self::toArray());
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @throws EnumException
     */
    public static function __callStatic($name, $arguments)
    {
        $constants = self::toArray();

        if (!isset($constants[$name])) {
            throw new EnumException(sprintf('%s does not defined in enum class %s', $name, get_called_class()));
        }

        return $constants[$name];
    }


    /**
     * @return array
     */
    private static function _getCurrentEnums()
    {
        $class = get_called_class();

        if (!isset(self::$_classInstances[$class])) {

            foreach (self::_getReflectClass()->getConstants() as $name => $value) {
                self::$_classInstances[$class][] = new $class($name, $value);
            }
        }

        return self::$_classInstances[$class];
    }

    /**
     * @return \ReflectionClass
     */
    private static function _getReflectClass()
    {
        $class = get_called_class();

        if (!isset(self::$_reflectClasses[$class])) {
            self::$_reflectClasses[$class] = new \ReflectionClass($class);
        }

        return self::$_reflectClasses[$class];
    }

    /**
     * @return array
     */
    public static function toArray()
    {
        $currentEnums = self::_getCurrentEnums();
        $enums = [];

        /** @var self $enum */
        foreach ($currentEnums as $enum) {
            $enums[$enum->getName()] = $enum->getValue();
        }

        return $enums;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return json_encode(self::toArray());
    }
}