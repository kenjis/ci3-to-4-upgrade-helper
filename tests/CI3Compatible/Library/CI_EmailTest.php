<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Kenjis\CI3Compatible\TestCase;

class CI_EmailTest extends TestCase
{
    public function test_initialize(): void
    {
        $email = new CI_Email();

        $config = [
            'protocol' => 'mail',
            'wordwrap' => false,
        ];
        $email->initialize($config);

        $ci4email = $email->getCI4Library();

        $this->assertSame('mail', $ci4email->protocol);
        $this->assertSame(false, $ci4email->wordWrap);
    }
}
