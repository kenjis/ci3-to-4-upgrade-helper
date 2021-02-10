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

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseConnection;
use Kenjis\CI3Compatible\Exception\NotImplementedException;
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
    public function count_all()
    {
        throw new NotSupportedException(
            'count_all() moved to CI_DB_query_builder. Use it.'
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
        if ($return_object !== null) {
            throw new NotImplementedException(
                '$return_object is not implemented yet.'
            );
        }

        $query = $this->db->query($sql, $binds);

        return new CI_DB_result($query);
    }

    /**
     * Start Transaction
     *
     * @param   bool $test_mode = FALSE
     *
     * @return  bool
     */
    public function trans_start(bool $test_mode = false): bool
    {
        return $this->db->transStart($test_mode);
    }

    /**
     * Complete Transaction
     *
     * @return  bool
     */
    public function trans_complete(): bool
    {
        return $this->db->transComplete();
    }

    /**
     * Lets you retrieve the transaction flag to determine if it has failed
     *
     * @return  bool
     */
    public function trans_status(): bool
    {
        return $this->db->transStatus();
    }

    /**
     * Begin Transaction
     *
     * @param   bool $test_mode
     *
     * @return  bool
     */
    public function trans_begin(bool $test_mode = false): bool
    {
        return $this->db->transBegin($test_mode);
    }

    /**
     * Commit Transaction
     *
     * @return  bool
     */
    public function trans_commit(): bool
    {
        return $this->db->transCommit();
    }

    /**
     * Rollback Transaction
     *
     * @return  bool
     */
    public function trans_rollback(): bool
    {
        return $this->db->transRollback();
    }

    /**
     * Enable/disable Transaction Strict Mode
     *
     * When strict mode is enabled, if you are running multiple groups of
     * transactions, if one group fails all subsequent groups will be
     * rolled back.
     *
     * If strict mode is disabled, each group is treated autonomously,
     * meaning a failure of one group will not affect any others
     *
     * @param   bool $mode = TRUE
     *
     * @return  void
     */
    public function trans_strict(bool $mode = true)
    {
        $this->db->transStart($mode);
    }

    /**
     * Disable Transactions
     * This permits transactions to be disabled at run-time.
     *
     * @return  void
     */
    public function trans_off()
    {
        $this->db->transOff();
    }
}
