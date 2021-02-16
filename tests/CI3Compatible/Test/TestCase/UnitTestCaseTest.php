<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Test\TestCase;

use App\Controllers\Test;
use App\Models\News_model;
use Kenjis\CI3Compatible\TestSupport\TestCase;

class UnitTestCaseTest extends TestCase
{
    public function test_newController()
    {
        $testCase = new UnitTestCase();
        $testCase->setUp();

        $controller = $testCase->newController(Test::class);

        $this->assertInstanceOf(Test::class, $controller);
    }

    public function test_newModel()
    {
        $testCase = new UnitTestCase();
        $testCase->setUp();

        $model = $testCase->newModel(News_model::class);

        $this->assertInstanceOf(News_model::class, $model);
    }
}
