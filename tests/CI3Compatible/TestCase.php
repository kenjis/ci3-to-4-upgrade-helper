<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use Kenjis\PhpUnitHelper\ReflectionHelper;
use Kenjis\PhpUnitHelper\TestDouble;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestDouble;
    use ReflectionHelper;
    use LogTestHelperTrait;
}
