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
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
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

        $form_validation = new CI_Form_validation();
        $email = new CI_Email();
        $instances = [
            'form_validation' => $form_validation,
            'email' => $email,
        ];
        $injector->injectMultiple($instances);

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $this->assertSame($form_validation, $controller->form_validation);
        $this->assertSame($email, $controller->email);
    }
}
