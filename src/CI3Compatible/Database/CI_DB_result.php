<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Database;

use CodeIgniter\Database\ResultInterface;

class CI_DB_result
{
    /** @var ResultInterface */
    private $result;

    public function __construct(ResultInterface $result)
    {
        $this->result = $result;
    }

    /**
     * Query result. "array" version.
     *
     * @return    array
     */
    public function result_array(): array
    {
        return $this->result->getResultArray();
    }

    /**
     * Returns a single result row - array version
     *
     * @return    array
     */
    public function row_array(int $n = 0): array
    {
        return $this->result->getRowArray($n);
    }
}
