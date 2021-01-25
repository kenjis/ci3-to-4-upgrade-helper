<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\TestCase;

class CI_InputTest extends TestCase
{
    /** @var CI_Input */
    private $input;

    public function setUp(): void
    {
        parent::setUp();

        $_POST = [];
        $this->input = new CI_Input();
    }


    public function test_post(): void
    {
        $_POST['q'] = 'abc';

        $val = $this->input->post('q');

        $this->assertSame('abc', $val);
    }

    public function test_server(): void
    {
        $val = $this->input->server('CI_ENVIRONMENT');

        $this->assertSame('testing', $val);
    }

    public function test_server_xss_clean(): void
    {
        $this->expectException(NotSupportedException::class);

        $this->input->server('CI_ENVIRONMENT', true);
    }
}
