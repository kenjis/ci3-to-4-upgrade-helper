<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestCase;

class LibraryLoaderTest extends TestCase
{
    public function test_load_ci3_library_form_validation(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);
        $loader = new LibraryLoader($injector);

        $loader->load('form_validation');

        $this->assertInstanceOf(
            CI_Form_validation::class,
            $controller->form_validation
        );
    }
}
