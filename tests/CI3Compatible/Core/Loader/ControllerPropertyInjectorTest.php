<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Core\CI_Output;
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
