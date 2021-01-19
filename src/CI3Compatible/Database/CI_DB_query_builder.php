<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseResult;
use Kenjis\CI3Compatible\Exception\LogicException;

use function is_bool;

class CI_DB_query_builder extends CI_DB_driver
{
    /** @var BaseBuilder */
    private $builder;

    /** @var array */
    private $order_by = [];

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
        $this->createQueryBuilder($table);

        $this->prepareSelectQuery();
        $query = $this->builder->get($limit, $offset);

        return new CI_DB_result($query);
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
        $this->createQueryBuilder($table);

        $this->prepareSelectQuery();
        $query = $this->builder->getWhere($where, $limit, $offset);

        return new CI_DB_result($query);
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
        $this->createQueryBuilder($table);

        $ret = $this->builder->insert($set, $escape);

        if ($ret instanceof BaseResult) {
            return true;
        }

        if (is_bool($ret)) {
            return $ret;
        }

        return false;
    }

    /**
     * ORDER BY
     *
     * @param   string $orderby
     * @param   string $direction ASC, DESC or RANDOM
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function order_by(
        string $orderby,
        string $direction = '',
        ?bool $escape = null
    ): self {
        $this->order_by[] = [$orderby, $direction, $escape];

        return $this;
    }

    private function prepareSelectQuery(): void
    {
        if ($this->builder === null) {
            throw new LogicException('$this->builder is not set');
        }

        foreach ($this->order_by as $params) {
            $this->builder->orderBy(...$params);
        }
    }

    /**
     * Get SELECT query string
     *
     * Compiles a SELECT query string and returns the sql.
     *
     * @param   string  the table name to select from (optional)
     * @param   bool    TRUE: resets QB values; FALSE: leave QB values alone
     *
     * @return  string
     */
    public function get_compiled_select($table = '', $reset = true): string
    {
        $this->createQueryBuilder($table);

        return $this->builder->getCompiledSelect($reset);
    }

    private function createQueryBuilder(string $table): void
    {
        if ($table !== '') {
            $this->builder = $this->db->table($table);
        }

        if ($this->builder === null) {
            throw new LogicException('$this->builder is not set');
        }
    }
}
