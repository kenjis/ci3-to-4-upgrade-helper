<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_forge;

abstract class CI_Migration extends Migration
{
    /** @var CI_DB */
    protected $db_;

    /** @var CI_DB_forge */
    protected $dbforge;

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);

        $this->db_ = new CI_DB($this->db);
        $this->dbforge = new CI_DB_forge();
    }
}
