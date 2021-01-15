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

        if ($object_name === null) {
            $object_name = $library;
        }

        $this->injector->inject($object_name, new $classname($params));
    }
}
