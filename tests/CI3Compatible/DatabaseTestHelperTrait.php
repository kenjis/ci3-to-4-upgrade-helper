<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Config\Database;

/**
 * Ported from CodeIgniter4 CIDatabaseTestCase
 *  (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * @see <https://github.com/codeigniter4/CodeIgniter4/blob/1e74d72cc7a688184a162b050c6ce9bbf010afa7/system/Test/CIDatabaseTestCase.php>
 * @see <https://github.com/codeigniter4/CodeIgniter4/blob/develop/LICENSE>
 */
trait DatabaseTestHelperTrait
{
    /**
     * Our database connection.
     *
     * @var BaseConnection
     */
    protected $db;

    /**
     * The name of the database group to connect to.
     * If not present, will use the defaultGroup.
     *
     * @var string
     */
    protected $DBGroup = 'tests';

    /**
     * Stores information needed to remove any
     * rows inserted via $this->hasInDatabase();
     *
     * @var array
     */
    protected $insertCache = [];

    /**
     * Load any database test dependencies.
     *
     * @before
     */
    public function loadDependencies(): void
    {
        if ($this->db === null) {
            $this->db = Database::connect($this->DBGroup);
            $this->db->initialize();
        }
    }

    //--------------------------------------------------------------------
    // Database Test Helpers
    //--------------------------------------------------------------------

    /**
     * Asserts that records that match the conditions in $where do
     * not exist in the database.
     *
     * @param string $table
     * @param array  $where
     *
     * @return void
     */
    public function dontSeeInDatabase(string $table, array $where): void
    {
        $count = $this->db->table($table)
            ->where($where)
            ->countAllResults();

        $this->assertTrue($count === 0, 'Row was found in database');
    }

    /**
     * Asserts that records that match the conditions in $where DO
     * exist in the database.
     *
     * @param string $table
     * @param array  $where
     *
     * @return void
     *
     * @throws DatabaseException
     */
    public function seeInDatabase(string $table, array $where): void
    {
        $count = $this->db->table($table)
            ->where($where)
            ->countAllResults();

        $this->assertTrue(
            $count > 0,
            'Row not found in database: ' . $this->db->showLastQuery()
        );
    }

    /**
     * Fetches a single column from a database row with criteria
     * matching $where.
     *
     * @param string $table
     * @param string $column
     * @param array  $where
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function grabFromDatabase(
        string $table,
        string $column,
        array $where
    ): bool {
        $query = $this->db->table($table)
            ->select($column)
            ->where($where)
            ->get();

        $query = $query->getRow();

        return $query->$column ?? false;
    }

    /**
     * Inserts a row into to the database. This row will be removed
     * after the test has run.
     *
     * @param string $table
     * @param array  $data
     *
     * @return bool
     */
    public function hasInDatabase(string $table, array $data): bool
    {
        $this->insertCache[] = [
            $table,
            $data,
        ];

        return $this->db->table($table)
            ->insert($data);
    }

    /**
     * Asserts that the number of rows in the database that match $where
     * is equal to $expected.
     *
     * @param int    $expected
     * @param string $table
     * @param array  $where
     *
     * @return void
     *
     * @throws DatabaseException
     */
    public function seeNumRecords(int $expected, string $table, array $where): void
    {
        $count = $this->db->table($table)
            ->where($where)
            ->countAllResults();

        $this->assertEquals(
            $expected,
            $count,
            'Wrong number of matching rows in database.'
        );
    }
}
