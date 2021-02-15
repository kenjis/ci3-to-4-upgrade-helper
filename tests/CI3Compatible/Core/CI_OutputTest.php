<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use CodeIgniter\HTTP\Response;
use Config\App;
use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\TestCase;

class CI_OutputTest extends TestCase
{
    /** @var CI_Output */
    private $output;

    public function setUp(): void
    {
        parent::setUp();

        $this->output = new CI_Output();
    }

    public function test_set_header(): void
    {
        $this->output->set_header('Content-Type: text/html; charset=Shift_JIS');

        $response = Services::response();
        $this->assertSame(
            'Content-Type: text/html; charset=Shift_JIS',
            (string) $response->header('Content-Type')
        );
    }

    public function test_set_header_append(): void
    {
        $this->output->set_header('Accept: json');
        $this->output->set_header('Accept: xml', false);

        $response = Services::response();
        $this->assertSame(
            'Accept: json, xml',
            (string) $response->header('Accept')
        );
    }

    public function test_set_header_replace(): void
    {
        $response = Services::response();
        $response->removeHeader('Cache-Control');

        $this->output->set_header('Cache-Control: no-cache');
        $this->output->set_header('Cache-Control: cache');

        $this->assertSame(
            'Cache-Control: cache',
            (string) $response->header('Cache-Control')
        );
    }

    public function test_get_output(): void
    {
        $response = new Response(new App());
        $body = 'This is HTTP body.';
        $response->setBody($body);
        Services::injectMock('response', $response);

        $output = $this->output->get_output();

        $this->assertSame($body, $output);
    }

    public function test_enable_profiler(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->output->enable_profiler();
    }
}
