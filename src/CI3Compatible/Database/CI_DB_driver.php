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

    /**
     * Execute the query
     *
     * Accepts an SQL string as input and returns a result object upon
     * successful execution of a "read" type query. Returns boolean TRUE
     * upon successful execution of a "write" type query. Returns boolean
     * FALSE upon failure, and if the $db_debug variable is set to TRUE
     * will raise an error.
     *
     * @param   string     $sql
     * @param   array|bool $binds         = FALSE      An array of binding data
     * @param   bool       $return_object = NULL
     *
     * @return  mixed
     */
    public function query(string $sql, $binds = false, ?bool $return_object = null)
    {
        $query = $this->db->query($sql, $binds);

        return new CI_DB_result($query);
    }
}
