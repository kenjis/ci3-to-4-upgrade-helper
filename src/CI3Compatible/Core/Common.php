<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

use CodeIgniter\Exceptions\PageNotFoundException;
use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\Exception\RuntimeException;

if (! function_exists('show_error')) {
    /**
     * Error Handler
     *
     * This function lets us invoke the exception class and
     * display errors using the standard error template located
     * in application/views/errors/error_general.php
     * This function will send the error page directly to the
     * browser and exit.
     *
     * @param   string
     * @param   int
     * @param   string
     *
     * @return  void
     */
    function show_error($message, $status_code = 500, $heading = '')
    {
        if ($heading !== '') {
            throw new NotSupportedException(
                '$heading is not supported.'
                . 'Please write your view file `app/Views/errors/html/error_500.php`.'
            );
        }

        throw new RuntimeException($message, $status_code);
    }
}

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
