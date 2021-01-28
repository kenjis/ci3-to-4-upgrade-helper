<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\CoreResolver;
use Kenjis\CI3Compatible\Core\Loader\ControllerPropertyInjector;
use Kenjis\CI3Compatible\Internal\DebugLog;
use ReflectionObject;

use function array_change_key_case;
use function get_class;
use function strtolower;

use const CASE_LOWER;

class CoreLoader
{
    /** @var CoreLoader */
    private static $instance;

    /** @var array */
    private $coreClasses = [
        // name w/o prefix => instance
        'Benchmark' => null,
        'Config' => null,
        'Input' => null,
        'Lang' => null,
        'Output' => null,
        'Security' => null,
        'URI' => null,
        'Utf8' => null,
    ];

    /** @var CoreResolver */
    private $resolver;

    /** @var CI_Loader */
    private $loader;

    public function __construct()
    {
        self::$instance = $this;

        $this->resolver = new CoreResolver();

        $this->load();
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    private function load(): void
    {
        foreach ($this->coreClasses as $class => $instance) {
            $classname = $this->resolver->resolve($class);
            $obj = new $classname();
            $this->coreClasses[$class] = $obj;
        }
    }

    /**
     * Inject Core Classes w/o CI_Loader to Controller
     */
    public function injectToController(ControllerPropertyInjector $injector): void
    {
        $array = array_change_key_case($this->coreClasses, CASE_LOWER);
        $injector->injectMultiple($array);
    }

    /**
     * Inject Core Classes
     */
    public function injectTo(object $obj): void
    {
        $reflection = new ReflectionObject($obj);
        $classname = get_class($obj);

        foreach ($this->coreClasses as $name => $instance) {
            $property = strtolower($name);

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

        if (! $reflection->hasProperty('load')) {
            $obj->load = $this->loader;
        }
    }

    public function setLoader(CI_Loader $loader): void
    {
        $this->loader = $loader;
    }
}
