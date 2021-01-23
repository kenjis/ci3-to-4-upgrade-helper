<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\UserAgent;
use Config\Services;

class CI_User_agent
{
    /** @var UserAgent */
    private $agent;

    /**
     * Constructor
     *
     * Sets the User Agent and runs the compilation routine
     *
     * @return  void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->agent = $request->getUserAgent();
    }

    /**
     * Is Mobile
     *
     * @param   string $key
     *
     * @return  bool
     */
    public function is_mobile(?string $key = null): bool
    {
        return $this->agent->isMobile($key);
    }
}
