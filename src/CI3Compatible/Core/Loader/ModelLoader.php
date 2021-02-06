<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\ModelResolver;
use Kenjis\CI3Compatible\Internal\DebugLog;
use ReflectionObject;

use function array_key_exists;
use function end;
use function explode;
use function get_class;
use function is_array;
use function is_int;

class ModelLoader
{
    use InSubDir;

    /** @var ModelResolver */
    private $modelResolver;

    /** @var ControllerPropertyInjector */
    private $injector;

    /** @var array<string, object> List of loaded classes [property_name => instance] */
    private $loadedClasses = [];

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;

        $this->modelResolver = new ModelResolver();
    }

    /**
     * @param mixed  $model
     * @param string $name
     */
    public function load($model, string $name = ''): void
    {
        if (empty($model)) {
            return;
        }

        if (is_array($model)) {
            $this->loadMultiple($model);

            return;
        }

        $this->loadOne($model, $name);
    }

    private function loadOne(string $model, string $name): void
    {
        $property = $this->getPropertyName($model, $name);

        if ($this->isLoaded($property)) {
            return;
        }

        $classname = $this->modelResolver->resolve($model);
        $instance = $this->createInstance($classname);

        $this->injector->inject($property, $instance);
        $this->loaded($property, $instance);
    }

    private function loaded(string $property, object $instance)
    {
        $this->loadedClasses[$property] = $instance;
    }

    private function isLoaded(string $property): bool
    {
        if (array_key_exists($property, $this->loadedClasses)) {
            return true;
        }

        return false;
    }

    private function loadMultiple(array $models): void
    {
        foreach ($models as $key => $value) {
            is_int($key)
                ? $this->load($value, '')
                : $this->load($key, $value);
        }
    }

    private function getPropertyName(string $model, string $name): string
    {
        if ($this->inSubDir($model)) {
            $parts = explode('/', $model);

            return end($parts);
        }

        if ($name === '') {
            return $model;
        }

        return $name;
    }

    private function createInstance(string $classname): object
    {
        $instance = model($classname, false);

        $message = 'Model "' . $classname . '" created';
        DebugLog::log(__METHOD__, $message);

        return $instance;
    }

    /**
     * Inject Loaded Classes
     */
    public function injectTo(object $obj): void
    {
        $reflection = new ReflectionObject($obj);
        $classname = get_class($obj);

        foreach ($this->loadedClasses as $property => $instance) {
            // Skip if the property exists
            if (! $reflection->hasProperty($property)) {
                $obj->$property = $instance;

                $message = $classname . '::$' . $property . ' injected';
                DebugLog::log(__METHOD__, $message);
            } else {
                $message = $classname . '::$' . $property . ' already exists';
                DebugLog::log(__METHOD__, $message);
            }
        }
    }
}
