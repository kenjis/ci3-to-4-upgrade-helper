<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\PhpUnitHelper\ReflectionHelper;
use Kenjis\PhpUnitHelper\TestDouble;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestDouble;
    use ReflectionHelper;
    use LogTestHelperTrait;

    public function setUp(): void
    {
        // Initialize controller
        new CI_Controller();
    }
}
