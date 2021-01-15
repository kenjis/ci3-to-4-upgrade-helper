<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestCase;

class LibaryResolverTest extends TestCase
{
    public function test_libary_name_is_string(): void
    {
        $resolver = new LibraryResolver();

        $classname = $resolver->resolve('form_validation');

        $this->assertSame(CI_Form_validation::class, $classname);
    }
}
