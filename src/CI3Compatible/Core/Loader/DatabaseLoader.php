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
