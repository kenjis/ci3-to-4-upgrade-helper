<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library\Upload;

use Kenjis\CI3Compatible\TestCase;

class FileExtenstionTest extends TestCase
{
    public function test_create_instance()
    {
        $fileExt = new FileExtention();

        $this->assertInstanceOf(FileExtention::class, $fileExt);
    }

    public function test_toLower_no_extention()
    {
        $fileExt = new FileExtention();

        $filename = $fileExt->toLower('ABC');

        $this->assertSame('ABC', $filename);
    }

    public function test_toLower_ext_upper_case()
    {
        $fileExt = new FileExtention();

        $filename = $fileExt->toLower('ABC.JPG');

        $this->assertSame('ABC.jpg', $filename);
    }

    public function test_toLower_ext_lower_case()
    {
        $fileExt = new FileExtention();

        $filename = $fileExt->toLower('ABC.png');

        $this->assertSame('ABC.png', $filename);
    }
}
