<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Email\Email;
use Config\Services;
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

    public function test_from(): void
    {
        $ci4email = $this->getDouble(
            Email::class,
            ['send' => true]
        );
        Services::injectMock('email', $ci4email);

        $email = new CI_Email();

        $from = 'foo@example.com';
        $from_name = 'Real Name';
        $email->from($from, $from_name);

        $headers = $this->getPrivateProperty($ci4email, 'headers');
        $this->assertSame('"Real Name" <foo@example.com>', $headers['From']);
    }

    public function test_to_string(): void
    {
        $email = new CI_Email();

        $to = 'info@example.jp';
        $email->to($to);

        $ci4email = $email->getCI4Library();

        $recipients = $this->getPrivateProperty($ci4email, 'recipients');
        $this->assertSame($to, $recipients[0]);
    }

    public function test_to_array(): void
    {
        $email = new CI_Email();

        $to = [
            'info@example.jp',
            'info@example.com',
        ];
        $email->to($to);

        $ci4email = $email->getCI4Library();

        $recipients = $this->getPrivateProperty($ci4email, 'recipients');
        $this->assertSame($to, $recipients);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Services::reset();
    }
}
