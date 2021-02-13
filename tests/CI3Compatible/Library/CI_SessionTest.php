<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Session\Handlers\FileHandler;
use CodeIgniter\Session\Session;
use CodeIgniter\Test\Mock\MockSession;
use CodeIgniter\Test\TestLogger;
use Config\App as AppConfig;
use Config\Logger;
use Config\Services;
use Kenjis\CI3Compatible\TestCase;

use function array_merge;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 */
class CI_SessionTest extends TestCase
{
    /** @var CI_Session */
    private $session;

    /** @var MockSession */
    private $ci4session;

    public function setUp(): void
    {
        parent::setUp();

        $_COOKIE  = [];
        $_SESSION = [];

        $this->createSession();
    }

    private function createSession()
    {
        $this->ci4session = $this->createCI4Session();
        Services::injectMock('session', $this->ci4session);

        $this->session = new CI_Session();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Services::reset(true);
    }

    private function createCI4Session($options = []): Session
    {
        $defaults = [
            'sessionDriver'            => 'CodeIgniter\Session\Handlers\FileHandler',
            'sessionCookieName'        => 'ci_session',
            'sessionExpiration'        => 7200,
            'sessionSavePath'          => null,
            'sessionMatchIP'           => false,
            'sessionTimeToUpdate'      => 300,
            'sessionRegenerateDestroy' => false,
            'cookieDomain'             => '',
            'cookiePrefix'             => '',
            'cookiePath'               => '/',
            'cookieSecure'             => false,
            'cookieSameSite'           => 'Lax',
        ];

        $config    = array_merge($defaults, $options);
        $appConfig = new AppConfig();
        foreach ($config as $key => $c) {
            $appConfig->$key = $c;
        }

        $session = new MockSession(new FileHandler($appConfig, '127.0.0.1'), $appConfig);
        $session->setLogger(new TestLogger(new Logger()));

        return $session;
    }

    public function test_get_ci4_session(): void
    {
        $this->assertSame($this->ci4session, $this->session->getCI4Library());
    }

    public function test_set_userdata(): void
    {
        $this->session->set_userdata('some_name', 'some_value');

        $this->assertSame('some_value', $_SESSION['some_name']);
    }

    public function test_set_userdata_magic_setter(): void
    {
        $this->session->some_name = 'some_value';

        $this->assertSame('some_value', $_SESSION['some_name']);
    }

    public function test_userdata_magic_getter(): void
    {
        $this->session->set_userdata('some_name', 'some_value');

        $this->assertSame('some_value', $this->session->some_name);
    }

    public function test_unset_userdata()
    {
        $this->session->set_userdata('some_name', 'some_value');

        $this->session->unset_userdata('some_name');

        $this->assertSame(null, $this->session->some_name);
    }

    public function test_set_flashdata(): void
    {
        $this->session->set_flashdata('flash', 'flash_value');

        $this->assertSame('flash_value', $_SESSION['flash']);
    }

    public function test_flashdata(): void
    {
        $this->session->set_flashdata('flash', 'flash_value');

        $this->assertSame(
            'flash_value',
            $this->session->flashdata('flash')
        );
    }
}
