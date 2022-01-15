<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Kenjis\CI3Compatible\TestSupport\TestCase;

class CI_LangTest extends TestCase
{
    /** @var CI_Lang */
    private $lang;

    public function setUp(): void
    {
        parent::setUp();

        $langPaths = [__DIR__ . '/../../App/Language'];
        $this->lang = new CI_Lang($langPaths);
    }

    public function test_load(): void
    {
        $filename = 'test';
        $lang = 'english';
        $lang = $this->lang->load($filename, $lang, true);

        $expected = [
            'message_key' => 'english message',
        ];
        $this->assertSame($expected, $lang);
    }

    public function test_load_get_default_locale(): void
    {
        $filename = 'test';
        $lang = $this->lang->load($filename, '', true);

        $expected = [
            'message_key' => 'english message',
        ];
        $this->assertSame($expected, $lang);
    }

    public function test_lang(): void
    {
        $filename = 'test';
        $lang = 'english';
        $this->lang->load($filename, $lang);

        $this->assertSame('english message', $this->lang->line('message_key'));
    }
}
