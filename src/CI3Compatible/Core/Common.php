<?php

declare(strict_types=1);

use CodeIgniter\Exceptions\PageNotFoundException;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

if (! function_exists('show_404')) {
    /**
     * @param bool $log_error @TODO not implemented
     */
    function show_404(string $page = '', bool $log_error = true): void
    {
        throw new PageNotFoundException($page);
    }
}

if (! function_exists('html_escape')) {
    /**
     * Returns HTML escaped variable.
     *
     * @param   mixed $var           The input string or array of strings to be escaped.
     * @param   bool  $double_encode $double_encode set to FALSE prevents escaping twice.
     *
     * @return  mixed           The escaped string or array of strings as a result.
     */
    function html_escape($var, bool $double_encode = true)
    {
        if ($double_encode === false) {
            throw new NotSupportedException(
                '$double_encode = false is not supported.'
            );
        }

        return esc($var, 'html');
    }
}
