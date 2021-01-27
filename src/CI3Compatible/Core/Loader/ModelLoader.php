<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\ModelResolver;
use ReflectionObject;

use function array_key_exists;
use function end;
use function explode;
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
     * @param mixed $model
     * @param bool  $db_conn @TODO not implemented
     */
    public function load($model, string $name = '', bool $db_conn = false): void
    {
        if (empty($model)) {
            return;
        }

        if (is_array($model)) {
            $this->loadMultiple($model, $db_conn);

            return;
        }

        $this->loadOne($model, $name, $db_conn);
    }

    private function loadOne(string $model, string $name, bool $db_conn): void
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

    private function loadMultiple(array $models, bool $db_conn): void
    {
        foreach ($models as $key => $value) {
            is_int($key)
                ? $this->load($value, '', $db_conn)
                : $this->load($key, $value, $db_conn);
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

        log_message('debug', 'Model "' . $classname . '" created');

        return $instance;
    }

    /**
     * Inject Loaded Classes
     */
    public function injectTo(object $obj): void
    {
        $reflection = new ReflectionObject($obj);

        foreach ($this->loadedClasses as $property => $instance) {
            // Skip if the property exists
            if (! $reflection->hasProperty($property)) {
                $obj->$property = $instance;
            }
        }
    }
}
