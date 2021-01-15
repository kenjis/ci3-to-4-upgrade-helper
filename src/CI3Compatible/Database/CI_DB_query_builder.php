<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseResult;
use Kenjis\CI3Compatible\Exception\NotImplementedException;

use function is_bool;

class CI_DB_query_builder extends CI_DB_driver
{
    /**
     * Get
     *
     * Compiles the select statement based on the other functions called
     * and runs the query
     *
     * @param   string  the table
     * @param   string  the limit clause
     * @param   string  the offset clause
     *
     * @return  CI_DB_result
     */
    public function get($table = '', $limit = null, $offset = 0): CI_DB_result
    {
        if ($table !== '') {
            $builder = $this->db->table($table);
            $query = $builder->get($limit, $offset);

            return new CI_DB_result($query);
        }

        // @TODO
        throw new NotImplementedException('Not implemented yet');
    }

    /**
     * get_where()
     *
     * Allows the where clause, limit and offset to be added directly
     *
     * @param   string       $table
     * @param   string|array $where
     * @param   int          $limit
     * @param   int          $offset
     *
     * @return  CI_DB_result
     */
    public function get_where(
        string $table = '',
        $where = null,
        ?int $limit = null,
        ?int $offset = null
    ): CI_DB_result {
        if ($table !== '') {
            $builder = $this->db->table($table);
            $query = $builder->getWhere($where, $limit, $offset);

            return new CI_DB_result($query);
        }

        // @TODO
        throw new NotImplementedException('Not implemented yet');
    }

    /**
     * Insert
     *
     * Compiles an insert string and runs the query
     *
     * @param   string $table  the table to insert data into
     * @param   array  $set    an associative array of insert values
     * @param   bool   $escape Whether to escape values and identifiers
     *
     * @return  bool    TRUE on success, FALSE on failure
     */
    public function insert(string $table = '', ?array $set = null, ?bool $escape = null): bool
    {
        if ($table !== '') {
            $builder = $this->db->table($table);
            $ret = $builder->insert($set, $escape);

            if ($ret instanceof BaseResult) {
                return true;
            }

            if (is_bool($ret)) {
                return $ret;
            }

            return false;
        }

        // @TODO
        throw new NotImplementedException('Not implemented yet');
    }
}
