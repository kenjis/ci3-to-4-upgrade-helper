<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Config\Database;
use Kenjis\CI3Compatible\Database\CI_DB;

class DatabaseLoader
{
    /** @var ControllerPropertyInjector */
    private $injector;

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;
    }

    public function load($params = '', $return = false, $query_builder = null)
    {
        $connection = Database::connect($params);

        $db = new CI_DB($connection);

        $this->injector->inject('db', $db);

        if ($return) {
            return $db;
        }

        return true;
    }
}
