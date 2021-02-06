<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use CodeIgniter\Exceptions\PageNotFoundException;
use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\TestCase;

use function html_escape;
use function show_404;
use function show_error;

class CommonTest extends TestCase
{
    public function test_show_404_throws_PageNotFoundException(): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('Not Found: The controller/method pair you requested was not found.');

        show_404('admin/login');
    }

    public function test_show_error_throws_RuntimeException(): void
    {
        $this->expectException(RuntimeException::class);

        show_error('Invalid Input');
    }

    public function test_show_error_throws_NotSupportedException(): void
    {
        $this->expectException(NotSupportedException::class);

        show_error(
            'Invalid Input',
            500,
            'An Error Was Encountered'
        );
    }

    public function test_html_escape(): void
    {
        $string = html_escape('<>&&');
        $this->assertSame('&lt;&gt;&amp;&amp;', $string);
    }

    public function test_html_escaper_throws_NotSupportedException(): void
    {
        $this->expectException(NotSupportedException::class);

        html_escape('<>&&', false);
    }
}
