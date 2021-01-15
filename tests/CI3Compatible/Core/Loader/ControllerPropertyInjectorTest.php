<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestCase;

class ControllerPropertyInjectorTest extends TestCase
{
    public function test_(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);

        $property = 'form_validation';
        $obj = new CI_Form_validation();
        $injector->inject($property, $obj);

        $this->assertSame($obj, $controller->$property);
    }
}
