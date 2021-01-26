<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Config\Database;
use Kenjis\CI3Compatible\Database\CI_DB;

class DatabaseLoader
{
    /** @var ControllerPropertyInjector */
    private $injector;

    /** @var CI_DB */
    private $db;

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;
    }

    public function load($params = '', $return = false, $query_builder = null)
    {
        if (
            $return === false && $query_builder === null
            && isset($this->db)
        ) {
            return false;
        }

        if ($return) {
            $connection = Database::connect($params, false);

            return new CI_DB($connection);
        }

        if ($this->db === null) {
            $connection = Database::connect($params, false);
            $this->db = new CI_DB($connection);
            $this->injector->inject('db', $this->db);

            return $this->db;
        }

        return false;
    }
}
