<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

namespace Kenjis\CI3Compatible\Test\Traits;

use CodeIgniter\CodeIgniter;
use CodeIgniter\Session\Handlers\ArrayHandler;
use CodeIgniter\Session\SessionInterface;
use CodeIgniter\Test\Mock\MockSession;
use Config\Services;

use function version_compare;

trait SessionTest
{
    /** @var SessionInterface */
    protected $mockSession;

    /**
     * @before
     */
    public function reset(): void
    {
        $this->initGlobalSession();

        $this->resetServices();
        $this->resetInstance();
    }

    private function initGlobalSession()
    {
        $_SESSION = $_SESSION ?? [];
    }

    /**
     * Pre-loads the mock session driver into $this->session.
     *
     * @before
     */
    public function mockSession(): void
    {
        $config = config('Session');

        if (version_compare(CodeIgniter::CI_VERSION, '4.4.0', '<')) {
            $config = config('App');
        }

        $this->mockSession = new MockSession(
            new ArrayHandler($config, '0.0.0.0'),
            $config
        );

        Services::injectMock('session', $this->mockSession);
    }
}
