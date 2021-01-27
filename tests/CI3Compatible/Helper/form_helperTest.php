<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Helper;

use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

class form_helperTest extends HelperTestCase
{
    public function test_validation_errors(): void
    {
        $this->loadHelper('form');

        $validation = Services::validation();
        $validation->reset();
        $validation->setRule('username', 'Username', 'required');
        $data = [];
        $validation->run($data);

        $errorHtml = validation_errors();
        $this->assertStringContainsString(
            'The Username field is required.',
            $errorHtml
        );
    }

    public function test_validation_errors_throws_exception(): void
    {
        $this->expectException(NotSupportedException::class);

        validation_errors('<div>', '</div>');
    }

    public function test_form_error(): void
    {
        $validation = Services::validation();
        $validation->reset();
        $validation->setRule('username', 'Username', 'required');
        $data = [];
        $validation->run($data);

        $errorHtml = form_error('username');
        $this->assertStringContainsString(
            'The Username field is required.',
            $errorHtml
        );
    }

    public function test_form_error_throws_exception(): void
    {
        $this->expectException(NotSupportedException::class);

        form_error('username', '<div>', '</div>');
    }
}
