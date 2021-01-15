<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseConnection;

class CI_DB_driver
{
    /** @var BaseConnection */
    protected $db;

    public function __construct(BaseConnection $db)
    {
        $this->db = $db;
    }
}
