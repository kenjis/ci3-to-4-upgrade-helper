<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestCase;

class LibraryLoaderTest extends TestCase
{
    /** @var News */
    private $controller;

    /** @var LibraryLoader */
    private $loader;

    public function setUp(): void
    {
        $this->controller = new News();
        $injector = new ControllerPropertyInjector($this->controller);
        $this->loader = new LibraryLoader($injector);
    }

    public function test_load_ci3_library_form_validation(): void
    {
        $this->loader->load('form_validation');

        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->form_validation
        );
    }

    public function test_load_ci3_library_twice_form_validation(): void
    {
        $this->loader->load('form_validation');
        $this->loader->load('form_validation');

        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->form_validation
        );
    }

    public function test_load_ci3_library_two_instances_form_validation(): void
    {
        $this->loader->load('form_validation', null, 'a');
        $this->loader->load('form_validation', null, 'b');

        $this->assertNotSame($this->controller->a, $this->controller->b);
        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->a
        );
        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->b
        );
    }
}
