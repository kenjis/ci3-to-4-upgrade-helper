<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Test\TestCase;

use App\Controllers\MY_Controller;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Test\TestCase\TestCase as CI3TestCase;
use Kenjis\CI3Compatible\TestSupport\TestCase;

use function get_instance;

class TestCaseTest extends TestCase
{
    public function test_resetInstance()
    {
        $testCase = new CI3TestCase();

        $oldController = get_instance();
        $testCase->resetInstance();
        $newController = get_instance();

        $this->assertNotSame($oldController, $newController);
        $this->assertInstanceOf(CI_Controller::class, $newController);
    }

    public function test_resetInstance_MY_Controller()
    {
        $testCase = new CI3TestCase();

        $oldController = get_instance();
        $testCase->resetInstance(true);
        $newController = get_instance();

        $this->assertNotSame($oldController, $newController);
        $this->assertInstanceOf(MY_Controller::class, $newController);
    }
}
