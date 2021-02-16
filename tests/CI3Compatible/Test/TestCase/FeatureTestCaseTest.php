<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Test\TestCase;

use Kenjis\CI3Compatible\TestCase;

class FeatureTestCaseTest extends TestCase
{
    public function test_request()
    {
        $testCase = new FeatureTestCase();
        $testCase->createRequest();
        $testCase->setUp();

        $output = $testCase->request('GET', 'test/index');

        $expected = 'App\Controllers\Test::index';
        $this->assertSame($expected, $output);
    }
}
