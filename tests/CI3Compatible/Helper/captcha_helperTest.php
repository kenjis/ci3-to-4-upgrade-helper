<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Helper;

use Kenjis\CI3Compatible\TestCase;

use function unlink;

class captcha_helperTest extends TestCase
{
    public function test_create_captcha(): void
    {
        require __DIR__ . '/../../../src/CI3Compatible/Helper/captcha_helper.php';

        $data = [
            'word'      => 'abcd',
            'img_path'  => __DIR__ . '/',
            'img_url'   => 'http://example.com/captcha/',
        ];
        $cap = create_captcha($data);

        $file = __DIR__ . '/' . $cap['filename'];
        $this->assertSame($data['word'], $cap['word']);
        $this->assertFileExists($file);

        unlink($file);
    }
}
