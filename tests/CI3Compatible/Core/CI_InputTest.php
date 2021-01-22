<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Kenjis\CI3Compatible\TestCase;

class CI_InputTest extends TestCase
{
    /** @var CI_Input */
    private $input;

    public function setUp(): void
    {
        parent::setUp();

        $this->input = new CI_Input();
    }

    public function test_server(): void
    {
        $val = $this->input->server('CI_ENVIRONMENT');

        $this->assertSame('testing', $val);
    }
}
