<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Seeder as CI4Seeder;
use Config\Database;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_forge;

use function array_map;
use function is_array;
use function is_string;

/**
 * Seeder
 *
 * based on Seeder in ci-phpunit-test
 *  <https://github.com/kenjis/ci-phpunit-test/blob/master/application/libraries/Seeder.php>
 *  and extends CI4's Seeder.
 */
class Seeder extends CI4Seeder
{
    /** @var CI_DB */
    protected $db_;

    /** @var CI_DB_forge */
    protected $dbforge;

    /** @var array */
    protected $depends = [];

    public function __construct(Database $config, ?BaseConnection $db = null)
    {
        parent::__construct($config, $db);

        $this->db_ = new CI_DB($this->db);
        $this->dbforge = new CI_DB_forge();
    }

    /**
     * Run another seeder
     *
     * @param string $seeder Seeder classname
     */
    public function call_(string $seeder, bool $callDependencies = true): void
    {
        if ($this->seedPath === null) {
            $this->seedPath = APPPATH . 'Database/Seeds/';
        }

        $obj = $this->loadSeeder($seeder);
        if ($callDependencies === true && $obj instanceof Seeder) {
            $obj->callDependencies($this->seedPath);
        }

        $obj->run();
    }

    /**
     * Get Seeder instance
     */
    protected function loadSeeder(string $seeder): Seeder
    {
        $file = $this->seedPath . $seeder . '.php';
        require_once $file;

        return new $seeder();
    }

    /**
     * Call dependency seeders
     */
    public function callDependencies(string $seedPath): void
    {
        foreach ($this->depends as $path => $seeders) {
            $this->seedPath = $seedPath;
            if (is_string($path)) {
                $this->setPath($path);
            }

            $this->callDependency($seeders);
        }

        $this->setPath($seedPath);
    }

    /**
     * Call dependency seeder
     *
     * @param string|array $seederName
     */
    protected function callDependency($seederName): void
    {
        if (is_array($seederName)) {
            array_map([$this, 'callDependency'], $seederName);

            return;
        }

        $seeder = $this->loadSeeder($seederName);
        if (is_string($this->seedPath)) {
            $seeder->setPath($this->seedPath);
        }

        $seeder->call_($seederName, true);
    }
}
