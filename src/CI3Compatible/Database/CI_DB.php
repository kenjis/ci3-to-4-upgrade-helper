<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

class CI_DB extends CI_DB_query_builder
{
    /**
     * Insert ID
     *
     * @return  int
     */
    public function insert_id(): int
    {
        return $this->db->insertID();
    }
}
