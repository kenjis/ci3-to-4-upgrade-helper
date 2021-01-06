<?php

declare(strict_types=1);

namespace Kenjis\Ci3To4MigrationHelper;

use PHPUnit\Framework\TestCase;

class Ci3To4MigrationHelperTest extends TestCase
{
    /** @var Ci3To4MigrationHelper */
    protected $ci3To4MigrationHelper;

    protected function setUp(): void
    {
        $this->ci3To4MigrationHelper = new Ci3To4MigrationHelper();
    }

    public function testIsInstanceOfCi3To4MigrationHelper(): void
    {
        $actual = $this->ci3To4MigrationHelper;
        $this->assertInstanceOf(Ci3To4MigrationHelper::class, $actual);
    }
}
