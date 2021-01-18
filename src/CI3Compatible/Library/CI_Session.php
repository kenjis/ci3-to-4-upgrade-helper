<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use function session_destroy;

class CI_Session
{
    /**
     * Session destroy
     *
     * Legacy CI_Session compatibility method
     *
     * @return  void
     */
    public function sess_destroy(): void
    {
        session_destroy();
    }
}
