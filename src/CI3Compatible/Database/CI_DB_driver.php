<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseConnection;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

class CI_DB_driver
{
    /** @var BaseConnection */
    protected $db;

    public function __construct(BaseConnection $db)
    {
        $this->db = $db;
    }

    /**
     * For debugging
     *
     * @internal
     */
    public function getBaseConnection(): BaseConnection
    {
        return $this->db;
    }

    /**
     * "Count All" query
     *
     * Generates a platform-specific query string that counts all records in
     * the specified database
     *
     * @param   string
     *
     * @return  int
     */
    public function count_all($table = ''): int
    {
        throw new NotSupportedException(
            'count_all() moved to CI_QueryBuilder. Use it.'
        );
    }
}
