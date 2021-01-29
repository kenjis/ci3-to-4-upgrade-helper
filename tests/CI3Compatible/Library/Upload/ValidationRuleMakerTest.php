<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library\Upload;

use Kenjis\CI3Compatible\TestCase;

class ValidationRuleMakerTest extends TestCase
{
    public function test_create_instance()
    {
        $maker = new ValidationRuleMaker();

        $this->assertInstanceOf(ValidationRuleMaker::class, $maker);
    }

    public function test_allowed_types()
    {
        $maker = new ValidationRuleMaker();

        $fieldName = 'image';
        $ci3Config = [
            'allowed_types' => 'jpg|jpeg|png|JPG|PNG|JPEG',
        ];
        $rule = $maker->convert($fieldName, $ci3Config);

        $expected = [
            'image' => 'uploaded[image]|ext_in[image,jpg,jpeg,png,JPG,PNG,JPEG]',
        ];
        $this->assertSame($expected, $rule);
    }

    public function test_max_size()
    {
        $maker = new ValidationRuleMaker();

        $fieldName = 'image';
        $ci3Config = [
            'max_size' => 3000,
        ];
        $rule = $maker->convert($fieldName, $ci3Config);

        $expected = [
            'image' => 'uploaded[image]|max_size[image,3000]',
        ];
        $this->assertSame($expected, $rule);
    }

    public function test_max_width_and_max_height()
    {
        $maker = new ValidationRuleMaker();

        $fieldName = 'image';
        $ci3Config = [
            'max_width'       => 300,
            'max_height'      => 150,
        ];
        $rule = $maker->convert($fieldName, $ci3Config);

        $expected = [
            'image' => 'uploaded[image]|max_dims[image,300,150]',
        ];
        $this->assertSame($expected, $rule);
    }

    public function test_max_width_zero_and_max_height_zero()
    {
        $maker = new ValidationRuleMaker();

        $fieldName = 'image';
        $ci3Config = [
            'max_width'       => 0,
            'max_height'      => 0,
        ];
        $rule = $maker->convert($fieldName, $ci3Config);

        $expected = [
            'image' => 'uploaded[image]',
        ];
        $this->assertSame($expected, $rule);
    }
}
