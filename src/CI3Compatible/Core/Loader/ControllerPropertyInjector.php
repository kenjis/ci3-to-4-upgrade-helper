<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\CI_Controller;

class ControllerPropertyInjector
{
    /** @var CI_Controller */
    private $controller;

    public function __construct(CI_Controller $controller)
    {
        $this->controller = $controller;
    }

    public function inject(string $property, object $obj): void
    {
        $this->controller->$property = $obj;
    }
}
