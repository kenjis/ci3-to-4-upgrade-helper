<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Email\Email;
use Config\Services;
use Kenjis\CI3Compatible\TestSupport\TestCase;

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

    public function test_subject(): void
    {
        $email = new CI_Email();

        $subject = 'Very good news';
        $email->subject($subject);

        $ci4email = $email->getCI4Library();
        $headers = $this->getPrivateProperty($ci4email, 'headers');
        $this->assertSame(
            '=?UTF-8?Q?Very=20good=20news?=',
            $headers['Subject']
        );
    }

    public function test_message(): void
    {
        $email = new CI_Email();

        $message = 'This is mail body.';
        $email->message($message);

        $ci4email = $email->getCI4Library();
        $body = $this->getPrivateProperty($ci4email, 'body');
        $this->assertSame($message, $body);
    }

    public function test_send_and_print_debugger(): void
    {
        $ci4email = $this->getDouble(
            Email::class,
            [
                'sendWithMail' => true,
                'sendWithSendmail' => true,
                'sendWithSmtp' => true,
            ]
        );
        Services::injectMock('email', $ci4email);

        $email = new CI_Email();

        $from = 'foo@example.com';
        $from_name = 'Real Name';
        $email->from($from, $from_name);

        $to = 'info@example.jp';
        $email->to($to);

        $subject = 'Very good news';
        $email->subject($subject);

        $message = 'This is mail body.';
        $email->message($message);

        $email->send(false);

        $debugPrintHeaders = $email->print_debugger(['headers']);
        $this->assertStringContainsString(
            'From: &quot;Real Name&quot; &lt;foo@example.com&gt;',
            $debugPrintHeaders
        );

        $debugPrintSubject = $email->print_debugger(['subject']);
        $this->assertStringContainsString(
            '=?utf-8?Q?=56=65=72=79=20=67=6F=6F=64=20=6E=65=77=73?=',
            $debugPrintSubject
        );

        $debugPrintBody = $email->print_debugger(['body']);
        $this->assertStringContainsString(
            'This is mail body.',
            $debugPrintBody
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Services::reset(true);
    }
}
