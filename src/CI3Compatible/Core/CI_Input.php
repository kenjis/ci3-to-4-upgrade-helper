<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

class CI_Input
{
    /**
     * Fetch an item from the POST array
     *
     * @param   mixed $index     Index for item to be fetched from $_POST
     * @param   bool  $xss_clean Whether to apply XSS filtering
     *
     * @return  mixed
     */
    public function post($index = null, bool $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);

        $request = Services::request();

        return $request->getPost($index);
    }

    private function checkXssClean(bool $xss_clean)
    {
        if ($xss_clean !== false) {
            throw new NotSupportedException(
                '$xss_clean is not supported.'
                . ' Preventing XSS should be performed on output, not input!'
                . 'Use esc() <https://codeigniter4.github.io/CodeIgniter4/general/common_functions.html#esc> instead.'
            );
        }
    }

    /**
     * Fetch an item from the SERVER array
     *
     * @param   mixed $index     Index for item to be fetched from $_SERVER
     * @param   bool  $xss_clean Whether to apply XSS filtering
     *
     * @return  mixed
     */
    public function server($index = null, bool $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);

        $request = Services::request();

        return $request->getServer($index);
    }
}
