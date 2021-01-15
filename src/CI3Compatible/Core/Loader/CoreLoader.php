<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\CoreResolver;

use function strtolower;

class CoreLoader
{
    /** @var array */
    private $coreClasses = [
        'Benchmark' => null,
        'Input' => null,
        'Lang' => null,
        'Output' => null,
        'Security' => null,
        'URI' => null,
        'Utf8' => null,
    ];

    /** @var CoreResolver */
    private $resolver;

    public function __construct()
    {
        $this->resolver = new CoreResolver();
    }

    public function load(): void
    {
        foreach ($this->coreClasses as $class => $instance) {
            $classname = $this->resolver->resolve($class);
            $obj = new $classname();
            $this->coreClasses[$class] = $obj;
        }
    }

    public function injectToController(ControllerPropertyInjector $injector): void
    {
        foreach ($this->coreClasses as $name => $instance) {
            $property = strtolower($name);
            $injector->inject($property, $instance);
        }
    }
}
