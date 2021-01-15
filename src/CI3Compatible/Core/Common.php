<?php

declare(strict_types=1);

use CodeIgniter\Exceptions\PageNotFoundException;

if (! function_exists('show_404')) {
    /**
     * @param bool $log_error @TODO not implemented
     */
    function show_404(string $page = '', bool $log_error = true): void
    {
        throw new PageNotFoundException($page);
    }
}
