<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Test\TestCase;

use Kenjis\CI3Compatible\TestSupport\TestCase;

class FeatureTestCaseTest extends TestCase
{
    /** @var FeatureTestCase */
    private $testCase;

    public function setUp(): void
    {
        parent::setUp();

        $this->testCase = new FeatureTestCase();
        $this->testCase->createRequest();
        $this->testCase->setUp();
    }

    public function test_request()
    {
        $output = $this->testCase->request('GET', 'test/index');

        $expected = 'App\Controllers\Test::index';
        $this->assertSame($expected, $output);
    }

    public function test_assertResponseCode_200()
    {
        $this->testCase->request('GET', 'test/index');

        $this->testCase->assertResponseCode(200);
    }

    public function test_assertResponseCode_404()
    {
        $this->testCase->request('GET', 'test/not-found');

        $this->testCase->assertResponseCode(404);
    }
}
