<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\LibraryResolver;

class LibraryLoader
{
    /** @var LibraryResolver */
    private $libraryResolver;

    /** @var ControllerPropertyInjector */
    private $injector;

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;

        $this->libraryResolver = new LibraryResolver();
    }

    public function load(
        $library,
        ?array $params = null,
        ?string $object_name = null
    ): void {
        $classname = $this->libraryResolver->resolve($library);
        $property = $this->getPropertyName($library, $object_name);
        $instance = $this->createInstance($classname, $params);

        $this->injector->inject($property, $instance);
    }

    private function getPropertyName(string $library, ?string $object_name): string
    {
        if ($object_name === null) {
            return $library;
        }

        return $object_name;
    }

    private function createInstance(string $classname, ?array $params): object
    {
        return new $classname($params);
    }
}
