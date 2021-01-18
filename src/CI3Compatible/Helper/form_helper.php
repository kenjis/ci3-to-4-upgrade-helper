<?php

declare(strict_types=1);

use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

if (! function_exists('validation_errors')) {
    /**
     * Validation Error String
     *
     * Returns all the errors associated with a form submission. This is a helper
     * function for the form validation class.
     *
     * @param   string
     * @param   string
     *
     * @return  string
     */
    function validation_errors($prefix = '', $suffix = ''): string
    {
        if ($prefix !== '' || $suffix !== '') {
            throw new NotSupportedException(
                '$prefix and $suffix are not supported.'
                . ' Create custom views to display errors.'
                . ' See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#customizing-error-display>.'
            );
        }

        return Services::validation()->listErrors();
    }
}
