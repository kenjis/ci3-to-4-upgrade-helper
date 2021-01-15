<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Config\Services;
use Kenjis\CI3Compatible\Exception\NotImplementedException;

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
        if ($xss_clean !== false) {
            throw new NotImplementedException(
                '$xss_clean is not implemented yet.'
            );
        }

        $request = Services::request();

        return $request->getPost($index);
    }
}
