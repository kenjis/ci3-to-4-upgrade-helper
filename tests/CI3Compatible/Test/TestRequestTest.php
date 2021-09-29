<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Test;

use App\Controllers\Test;
use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
use Kenjis\CI3Compatible\TestSupport\TestCase;

use function property_exists;

class TestRequestTest extends TestCase
{
    /** @var TestRequest */
    private $request;

    public function setUp(): void
    {
        parent::setUp();

        $featureTestCase = new FeatureTestCase();
        $featureTestCase->setUp();
        $this->request = new TestRequest($featureTestCase);
    }

    public function test_request()
    {
        $output = $this->request->request('GET', 'test/index');

        $expected = 'App\Controllers\Test::index';
        $this->assertSame($expected, $output);
    }

    public function test_redirect()
    {
        $output = $this->request->request('GET', 'test/redirect');

        $expected = '';
        $this->assertSame($expected, $output);

        $this->request->assertRedirect('/', 302);
    }

    public function test_setCallable()
    {
        $controller = new Test();

        $this->request->setCallable(
            static function ($CI) {
                $CI->foo = 'foo';
            }
        );
        $this->request->runCallables();

        $this->assertSame('foo', $controller->foo);
    }

    public function test_setCallable_reset_previous_callable()
    {
        $controller = new Test();

        $this->request->addCallable(
            static function ($CI) {
                $CI->foo = 'foo';
            }
        );
        $this->request->setCallable(
            static function ($CI) {
                $CI->bar = 'bar';
            }
        );
        $this->request->runCallables();

        $this->assertSame('bar', $controller->bar);
        $this->assertFalse(property_exists($controller, 'foo'));
    }

    public function test_addCallable()
    {
        $controller = new Test();

        $this->request->addCallable(
            static function ($CI) {
                $CI->foo = 'foo';
            }
        );
        $this->request->addCallable(
            static function ($CI) {
                $CI->bar = 'bar';
            }
        );
        $this->request->runCallables();

        $this->assertSame('foo', $controller->foo);
        $this->assertSame('bar', $controller->bar);
    }

    public function test_getInstance()
    {
        $instance = TestRequest::getInstance();

        $this->assertSame($this->request, $instance);
    }
}
