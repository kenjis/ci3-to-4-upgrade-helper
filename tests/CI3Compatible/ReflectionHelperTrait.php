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
    public static function getPrivateMethodInvoker($obj, string $method): Closure
    {
        $refMethod = new ReflectionMethod($obj, $method);
        $refMethod->setAccessible(true);
        $obj = gettype($obj) === 'object' ? $obj : null;

        return static function () use ($obj, $refMethod) {
            $args = func_get_args();

            return $refMethod->invokeArgs($obj, $args);
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
            $refClass = new ReflectionObject($obj);
        } else {
            $refClass = new ReflectionClass($obj);
        }

        $refProperty = $refClass->getProperty($property);
        $refProperty->setAccessible(true);

        return $refProperty;
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
        $refProperty = self::getAccessibleRefProperty($obj, $property);
        $refProperty->setValue($obj, $value);
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
        $refProperty = self::getAccessibleRefProperty($obj, $property);

        return $refProperty->getValue($obj);
    }
}
