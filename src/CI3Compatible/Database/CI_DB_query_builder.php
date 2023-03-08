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

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseResult;
use Kenjis\CI3Compatible\Exception\LogicException;

use function array_shift;
use function explode;
use function is_bool;
use function is_string;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class CI_DB_query_builder extends CI_DB_driver
{
    /** @var ?BaseBuilder */
    private $builder;

    /** @var array */
    private $where = [];

    /** @var array */
    private $order_by = [];

    /** @var array */
    private $select = [];

    /** @var array */
    private $like = [];

    /** @var array */
    private $from = [];

    /** @var array */
    private $join = [];

    /** @var array */
    private $set = [];

    /** @var array */
    private $select_sum = [];

    /** @var array */
    private $group_by = [];

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
        if ($limit !== null) {
            $limit = (int) $limit;
        }

        $offset = (int) $offset;

        $this->ensureQueryBuilder($table);

        $this->prepareSelectQuery();
        $query = $this->builder->get($limit, $offset);

        $this->_reset_select();

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
        $this->ensureQueryBuilder($table);

        $this->prepareSelectQuery();
        $query = $this->builder->getWhere($where, $limit, $offset);

        $this->_reset_select();

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
        $this->ensureQueryBuilder($table);

        $this->prepareInsertQuery();
        $ret = $this->builder->insert($set, $escape);

        $this->_reset_write();

        if ($ret instanceof BaseResult) {
            return true;
        }

        if (is_bool($ret)) {
            return $ret;
        }

        return false;
    }

    /**
     * Insert_Batch
     *
     * Compiles batch insert strings and runs the queries
     *
     * @param   string $table  Table to insert into
     * @param   array  $set    An associative array of insert values
     * @param   bool   $escape Whether to escape values and identifiers
     *
     * @return  int|bool    Number of rows inserted or FALSE on failure
     */
    public function insert_batch(string $table, ?array $set = null, ?bool $escape = null, $batch_size = 100)
    {
        $this->ensureQueryBuilder($table);

        $ret = $this->builder->insertBatch($set, $escape, $batch_size);

        $this->_reset_write();

        return $ret;
    }

    /**
     * UPDATE
     *
     * Compiles an update string and runs the query.
     *
     * @param   string $table
     * @param   array  $set   An associative array of update values
     * @param   mixed  $where
     * @param   int    $limit
     *
     * @return  bool    TRUE on success, FALSE on failure
     */
    public function update(string $table = '', ?array $set = null, $where = null, ?int $limit = null)
    {
        $this->ensureQueryBuilder($table);

        $this->prepareUpdateQuery();
        $ret = $this->builder->update($set, $where, $limit);

        $this->_reset_write();

        return $ret;
    }

    /**
     * The "set" function.
     *
     * Allows key/value pairs to be set for inserting or updating
     *
     * @param   mixed
     * @param   string
     * @param   bool
     *
     * @return  CI_DB_query_builder
     */
    public function set($key, $value = '', $escape = null)
    {
        $this->set[] = [$key, $value, $escape];

        return $this;
    }

    /**
     * WHERE
     *
     * Generates the WHERE portion of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param   mixed
     * @param   mixed
     * @param   bool
     *
     * @return  CI_DB_query_builder
     */
    public function where($key, $value = null, $escape = null): self
    {
        $this->where[] = ['where', $key, $value, $escape];

        return $this;
    }

    /**
     * OR WHERE
     *
     * Generates the WHERE portion of the query.
     * Separates multiple calls with 'OR'.
     *
     * @param   mixed
     * @param   mixed
     * @param   bool
     *
     * @return  CI_DB_query_builder
     */
    public function or_where($key, $value = null, $escape = null)
    {
        $this->where[] = ['orWhere', $key, $value, $escape];

        return $this;
    }

    /**
     * WHERE IN
     *
     * Generates a WHERE field IN('item', 'item') SQL query,
     * joined with 'AND' if appropriate.
     *
     * @param   string $key    The field to search
     * @param   array  $values The values searched on
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function where_in(?string $key = null, ?array $values = null, ?bool $escape = null)
    {
        $this->where[] = ['whereIn', $key, $values, $escape];

        return $this;
    }

    /**
     * OR WHERE IN
     *
     * Generates a WHERE field IN('item', 'item') SQL query,
     * joined with 'OR' if appropriate.
     *
     * @param   string $key    The field to search
     * @param   array  $values The values searched on
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function or_where_in(?string $key = null, ?array $values = null, ?bool $escape = null)
    {
        $this->where[] = ['orWhereIn', $key, $values, $escape];

        return $this;
    }

    /**
     * WHERE NOT IN
     *
     * Generates a WHERE field NOT IN('item', 'item') SQL query,
     * joined with 'AND' if appropriate.
     *
     * @param   string $key    The field to search
     * @param   array  $values The values searched on
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function where_not_in(?string $key = null, ?array $values = null, ?bool $escape = null)
    {
        $this->where[] = ['whereNotIn', $key, $values, $escape];

        return $this;
    }

    /**
     * OR WHERE NOT IN
     *
     * Generates a WHERE field NOT IN('item', 'item') SQL query,
     * joined with 'OR' if appropriate.
     *
     * @param   string $key    The field to search
     * @param   array  $values The values searched on
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function or_where_not_in(?string $key = null, ?array $values = null, ?bool $escape = null)
    {
        $this->where[] = ['orWhereNotIn', $key, $values, $escape];

        return $this;
    }

    /**
     * JOIN
     *
     * Generates the JOIN portion of the query
     *
     * @param   string
     * @param   string  the join condition
     * @param   string  the type of join
     * @param   string  whether not to try to escape identifiers
     *
     * @return  CI_DB_query_builder
     */
    public function join($table, $cond, $type = '', $escape = null): self
    {
        $this->join[] = [$table, $cond, $type, $escape];

        return $this;
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

    /**
     * GROUP BY
     *
     * @param mixed $by     what field or fields to group_by
     * @param bool  $escape
     *
     * @return CI_DB_query_builder
     */
    public function group_by(mixed $by, ?bool $escape = null): self
    {
        if (is_string($by))
        {
            $by = $escape === true
                ? explode(',', $by)
                : [$by];
        }
        $this->group_by = $by;

        return $this;
    }

    private function prepareSelectQuery(): void
    {
        $this->existsBuilder();

        foreach ($this->select as $params) {
            $this->builder->select(...$params);
        }

        foreach ($this->select_sum as $params) {
            $this->builder->selectSum(...$params);
        }

        $this->execJoin();
        $this->execWhere();
        $this->execLike();

        foreach ($this->order_by as $params) {
            $this->builder->orderBy(...$params);
        }

        $this->builder->groupBy($this->group_by);
    }

    private function prepareUpdateQuery(): void
    {
        $this->existsBuilder();

        $this->execSet();
        $this->execJoin();
        $this->execWhere();
        $this->execLike();
    }

    private function prepareInsertQuery()
    {
        $this->existsBuilder();

        $this->execSet();
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
        $this->ensureQueryBuilder($table);

        $this->prepareSelectQuery();
        $sql = $this->builder->getCompiledSelect($reset);

        if ($reset === true) {
            $this->_reset_select();
        }

        return $sql;
    }

    /**
     * Get UPDATE query string
     *
     * Compiles an update query and returns the sql
     *
     * @param   string  the table to update
     * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
     *
     * @return  string
     */
    public function get_compiled_update($table = '', $reset = true)
    {
        $this->ensureQueryBuilder($table);

        $this->prepareUpdateQuery();
        $sql = $this->builder->getCompiledUpdate($reset);

        if ($reset === true) {
            $this->_reset_write();
        }

        return $sql;
    }

    /**
     * Get INSERT query string
     *
     * Compiles an insert query and returns the sql
     *
     * @param   string  the table to insert into
     * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
     *
     * @return  string
     */
    public function get_compiled_insert($table = '', $reset = true)
    {
        $this->ensureQueryBuilder($table);

        $this->prepareInsertQuery();
        $sql = $this->builder->getCompiledInsert($reset);

        if ($reset === true) {
            $this->_reset_write();
        }

        return $sql;
    }

    /**
     * Get DELETE query string
     *
     * Compiles a delete query string and returns the sql
     *
     * @param   string  the table to delete from
     * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
     *
     * @return  string
     */
    public function get_compiled_delete($table = '', $reset = true)
    {
        $this->ensureQueryBuilder($table);

        $this->prepareDeleteQuery();
        $sql = $this->builder->getCompiledDelete($reset);

        if ($reset === true) {
            $this->_reset_write();
        }

        return $sql;
    }

    private function ensureQueryBuilder(string $table): void
    {
        if ($table !== '') {
            $this->builder = $this->db->table($table);
        }

        if ($this->builder === null) {
            throw new LogicException('$this->builder is not set');
        }
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
        $this->ensureQueryBuilder($table);

        $count = $this->builder->countAll();

        $this->_reset_select();

        return $count;
    }

    /**
     * Delete
     *
     * Compiles a delete string and runs the query
     *
     * @param   mixed   the table(s) to delete from. String or array
     * @param   mixed   the where clause
     * @param   mixed   the limit clause
     * @param   bool
     *
     * @return  mixed
     */
    public function delete($table = '', $where = '', $limit = null, $reset_data = true)
    {
        $this->ensureQueryBuilder($table);

        $this->prepareDeleteQuery();
        $ret = $this->builder->delete($where, $limit, $reset_data);

        if ($reset_data) {
            $this->_reset_write();
        }

        if ($ret instanceof BaseResult) {
            return new CI_DB_result($ret);
        }

        return $ret;
    }

    private function prepareDeleteQuery(): void
    {
        $this->existsBuilder();
        $this->execWhere();
        $this->execLike();
    }

    private function existsBuilder(): void
    {
        if ($this->builder === null) {
            throw new LogicException('$this->builder is not set');
        }
    }

    private function execSet(): void
    {
        foreach ($this->set as $params) {
            $this->builder->set(...$params);
        }
    }

    private function execJoin(): void
    {
        foreach ($this->join as $params) {
            $this->builder->join(...$params);
        }
    }

    private function execWhere(): void
    {
        foreach ($this->where as $params) {
            $method = array_shift($params);
            $this->builder->$method(...$params);
        }
    }

    private function execLike(): void
    {
        foreach ($this->like as $params) {
            $this->builder->like(...$params);
        }
    }

    /**
     * Select
     *
     * Generates the SELECT portion of the query
     *
     * @param   string
     * @param   mixed
     *
     * @return  CI_DB_query_builder
     */
    public function select($select = '*', $escape = null): self
    {
        $this->select[] = [$select, $escape];

        return $this;
    }

    /**
     * Select Sum
     *
     * Generates a SELECT SUM(field) portion of a query
     *
     * @param   string  the field
     * @param   string  an alias
     *
     * @return  CI_DB_query_builder
     */
    public function select_sum($select = '', $alias = '')
    {
        $this->select_sum[] = [$select, $alias];

        return $this;
    }

    /**
     * LIKE
     *
     * Generates a %LIKE% portion of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param   mixed  $field
     * @param   string $match
     * @param   string $side
     * @param   bool   $escape
     *
     * @return  CI_DB_query_builder
     */
    public function like(
        $field,
        string $match = '',
        string $side = 'both',
        ?bool $escape = null
    ): self {
        $this->like[] = [$field, $match, $side, $escape];

        return $this;
    }

    /**
     * Resets the query builder values.  Called by the get() function
     *
     * @return  void
     */
    private function _reset_select()
    {
        $this->builder = null;

        $this->select = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
        $this->like = [];
        $this->order_by = [];
    }

    /**
     * Resets the query builder "write" values.
     *
     * Called by the insert() update() insert_batch() update_batch() and delete() functions
     *
     * @return  void
     */
    protected function _reset_write()
    {
        $this->builder = null;

        $this->set = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
        $this->like = [];
        $this->order_by = [];
    }

    /**
     * Truncate
     *
     * Compiles a truncate string and runs the query
     * If the database does not support the truncate() command
     * This function maps to "DELETE FROM table"
     *
     * @param   string  the table to truncate
     *
     * @return  bool    TRUE on success, FALSE on failure
     *
     * @TODO @return is accutually BaseResult|false, but CI3 also returns Result
     */
    public function truncate($table = '')
    {
        $this->ensureQueryBuilder($table);

        $ret = $this->builder->truncate();

        $this->_reset_write();

        return $ret;
    }

    /**
     * From
     *
     * Generates the FROM portion of the query
     *
     * @param   mixed $from can be a string or array
     *
     * @return  CI_DB_query_builder
     */
    public function from($from): self
    {
        $this->from[] = $from;

        if ($this->builder === null && is_string($from)) {
            $this->builder = $this->db->table($from);
        }

        return $this;
    }
}
