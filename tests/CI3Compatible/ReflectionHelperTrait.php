<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;

use function func_get_args;
use function gettype;
use function is_object;

trait ReflectionHelperTrait
{
    /**
     * @param object|string $obj    object or class name
     * @param string        $method method name
     *
     * @return Closure
     *
     * @throws ReflectionException
     */
    public static function getPrivateMethodInvoker($obj, string $method): closure
    {
        $ref_method = new ReflectionMethod($obj, $method);
        $ref_method->setAccessible(true);
        $obj = gettype($obj) === 'object' ? $obj : null;

        return static function () use ($obj, $ref_method) {
            $args = func_get_args();

            return $ref_method->invokeArgs($obj, $args);
        };
    }

    /**
     * @param object|string $obj
     * @param string        $property
     *
     * @return ReflectionProperty
     *
     * @throws ReflectionException
     */
    protected static function getAccessibleRefProperty($obj, string $property): ReflectionProperty
    {
        if (is_object($obj)) {
            $ref_class = new ReflectionObject($obj);
        } else {
            $ref_class = new ReflectionClass($obj);
        }

        $ref_property = $ref_class->getProperty($property);
        $ref_property->setAccessible(true);

        return $ref_property;
    }

    /**
     * @param object|string $obj      object or class name
     * @param string        $property property name
     * @param mixed         $value    value
     *
     * @throws ReflectionException
     */
    public static function setPrivateProperty($obj, string $property, $value): void
    {
        $ref_property = self::getAccessibleRefProperty($obj, $property);
        $ref_property->setValue($obj, $value);
    }

    /**
     * @param object|string $obj      object or class name
     * @param string        $property property name
     *
     * @return mixed value
     *
     * @throws ReflectionException
     */
    public static function getPrivateProperty($obj, string $property)
    {
        $ref_property = self::getAccessibleRefProperty($obj, $property);

        return $ref_property->getValue($obj);
    }
}
