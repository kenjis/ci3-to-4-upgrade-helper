<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestDoubleTrait;
    use ReflectionHelperTrait;
}
