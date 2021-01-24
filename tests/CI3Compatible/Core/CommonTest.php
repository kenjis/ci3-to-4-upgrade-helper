<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use CodeIgniter\Exceptions\PageNotFoundException;
use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\TestCase;

class CommonTest extends TestCase
{
    public function test_show_404_throws_PageNotFoundException(): void
    {
        require __DIR__ . '/../../../src/CI3Compatible/Core/Common.php';

        $this->expectException(PageNotFoundException::class);

        show_404('Not Found');
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
