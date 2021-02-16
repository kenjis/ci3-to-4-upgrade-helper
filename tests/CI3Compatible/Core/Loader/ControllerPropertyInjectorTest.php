<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestSupport\TestCase;

class ControllerPropertyInjectorTest extends TestCase
{
    public function test_inject(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);

        $property = 'form_validation';
        $obj = new CI_Form_validation();
        $injector->inject($property, $obj);

        $this->assertSame($obj, $controller->$property);
    }

    public function test_inject_do_not_inject_existing_property(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);
        $controller->form_validation = new CI_Form_validation();

        $property = 'form_validation';
        $obj = new CI_Form_validation();
        $injector->inject($property, $obj);

        $this->assertNotSame($obj, $controller->$property);
    }

    public function test_injectMultiple(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);

        $input = new CI_Input();
        $output = new CI_Output();
        $instances = [
            'input' => $input,
            'output' => $output,
        ];
        $injector->injectMultiple($instances);

        $this->assertSame($input, $controller->input);
        $this->assertSame($output, $controller->output);
    }
}
