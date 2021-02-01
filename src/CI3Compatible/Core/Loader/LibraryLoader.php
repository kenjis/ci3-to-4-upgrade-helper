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

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\LibraryResolver;
use Kenjis\CI3Compatible\Internal\DebugLog;
use ReflectionObject;

use function array_key_exists;
use function end;
use function explode;
use function get_class;
use function is_array;
use function is_int;

class LibraryLoader
{
    use InSubDir;

    /** @var LibraryResolver */
    private $libraryResolver;

    /** @var ControllerPropertyInjector */
    private $injector;

    /** @var array<string, object> List of loaded classes [property_name => instance] */
    private $loadedClasses = [];

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;

        $this->libraryResolver = new LibraryResolver();
    }

    /**
     * @param string|array $libraries
     * @param array|null   $params
     * @param string|null  $object_name
     */
    public function load(
        $libraries,
        ?array $params = null,
        ?string $object_name = null
    ): void {
        if (empty($libraries)) {
            return;
        }

        if (is_array($libraries)) {
            $this->loadMultiple($libraries, $params);

            return;
        }

        if ($params !== null && ! is_array($params)) {
            $params = null;
        }

        $library = $libraries;
        $this->loadOne($library, $params, $object_name);
    }

    private function loadOne(
        string $library,
        ?array $params = null,
        ?string $object_name = null
    ): void {
        $property = $this->getPropertyName($library, $object_name);

        if ($this->isLoaded($property)) {
            return;
        }

        $classname = $this->libraryResolver->resolve($library);
        $instance = $this->createInstance($classname, $params);

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

    private function loadMultiple(array $libraries, ?array $params): void
    {
        foreach ($libraries as $key => $value) {
            if (is_int($key)) {
                $this->load($value, $params);
            } else {
                $this->load($key, $params, $value);
            }
        }
    }

    private function getPropertyName(string $library, ?string $object_name): string
    {
        if ($object_name === null) {
            if ($this->inSubDir($library)) {
                $parts = explode('/', $library);

                return end($parts);
            }

            if ($library === 'user_agent') {
                return 'agent';
            }

            return $library;
        }

        return $object_name;
    }

    private function createInstance(string $classname, ?array $params): object
    {
        $instance = new $classname($params);

        $message = 'Library "' . $classname . '" created';
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
