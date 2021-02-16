<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Config\Services;
use Kenjis\CI3Compatible\TestSupport\TestCase;

class CI_User_agentTest extends TestCase
{
    public function test_is_mobile(): void
    {
        Services::reset(true);
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586).';

        $agent = new CI_User_agent();

        $ret = $agent->is_mobile();

        $this->assertFalse($ret);
    }
}
